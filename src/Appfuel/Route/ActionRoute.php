<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use LogicException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData;

class ActionRoute implements ActionRouteInterface
{
    /**  
     * @var RouteSpecInterface
     */ 
    protected $spec = null;

    /**
     * List of action routes
     * @var array
     */
    protected $routes = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct(RouteSpecInterface $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return  string
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @return  string
     */
    public function getKey()
    {
        return $this->getSpec()
                    ->getKey();
    }

    /**
     * @param   string  $path
     * @param   ArrayDataInterface  $captures
     * @return  array
     */
    public function match(UriMatcherInterface $matcher)
    {
        $spec = $this->getSpec();
        if (! $spec->isUriSchemeAllowed($matcher->getUriScheme())) {
            return false;
        }

        if (! $spec->isHttpMethodAllowed($matcher->getHttpMethod())) {
            return false;
        }

        $matches = array();
        if (! $matcher->match($spec->getPattern(), $spec->getParams())) {
            return false;
        }

        if (empty($this->routes)) {
            return new MatchedRoute($spec, $matcher->getCaptures());
        }

        $found = false;
        foreach ($this->routes as $route) {
            if (false !== $matched = $route->match($matcher)) {
                $found = true;
                break;
            }
        }

        if (false === $found) {
            $matched = new MatchedRoute($spec, $matcher->getCaptures());
        }

        return $matched;
    }

    /**
     * Will only add to this collection or a child of this collection
     * @param   ActionRouteInterface    $route
     * @return  RouteCollection
     */
    public function add(ActionRouteInterface $route)
    {
        $myKey = $this->getKey();
        $routeKey  = $route->getKey();
        $targetKey = $this->validateKey($routeKey);

        $level = substr_count($targetKey, '.');
        if (0 === $level) {
            $this->routes[$targetKey] = $route;
            return $this;
        }

        $keys = $this->extractKeys($targetKey);
        
        /* 
         * remove the last key because we need the parent. when we have 
         * have the parent we can add the child
         */
        array_pop($keys);
        $parent = null;
        if (1 === count($keys) && isset($this->routes[$keys[0]])) {
            $parent = $this->routes[$keys[0]];
        }
        else {
            $routes = $this->routes;
            foreach ($keys as $key) {
                if (is_array($routes) && isset($routes[$key])) {
                    $routes = $routes[$key];
                }
                else if (is_object($routes)) {
                    $routes = $routes->getDirect($key);
                }
            }

            if ($routes instanceof ActionRouteInterface) {
                $parent = $routes;
            }
        }

        if (! $parent) {
            $err =  "can not add route. could not find -(key=$routeKey)";
            throw new LogicException($err);
        }
   
        $parent->add($route); 
        return $this;
    }

    /**
     * @param   string  $key
     * @return  ActionRouteInterface | false
     */
    public function get($routeKey)
    {
        $targetKey = $this->validateKey($routeKey);
    
        /* check if key is a direct child first */
        if (isset($this->routes[$routeKey])) {
            return $this->routes[$routeKey];
        }

        if (isset($this->routes[$targetKey])) {
            return $this->routes[$targetKey];
        }

        $keys = $this->extractKeys($targetKey);

        $target = array_pop($keys);
        $routes = $this->routes;
        foreach ($keys as $key) {
            if (is_array($routes) && isset($routes[$key])) {
                $routes = $routes[$key];
            }
            else if (is_object($routes)) {
                    $routes = $routes->getDirect($key);
            }
        }

        if (! $routes instanceof ActionRouteInterface) {
            return false;
        }

        return $routes->getDirect($target);
    }

    /**
     * @param   string  $name
     * @return  RouteCollectionInterface | false
     */
    public function getDirect($name)
    {
         if (! $this->isValidString($name)) {
            $err = "route key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->routes[$name])) {
            return false;
        }

        return $this->routes[$name];
    }

    /**
     * @param   string  $key
     * @return  array
     */
    protected function extractKeys($key)
    {
        if (false === $keys = explode('.', $key)) {
            $err = "could not extract keys from -($key) explode failed";
            throw new LogicException($err);
        }

        return $keys;
    }

    /**
     * Used for adding and getting sub routes. It ensures the route key is 
     * valid and extracts the relative position of the route key to object's
     * route key
     *
     * @var string  $routeKey
     * @return  string
     */
    protected function validateKey($routeKey)
    {
        if (! $this->isValidString($routeKey)) {
            $err = "route key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $myKey = $this->getKey();
        if ($myKey === $routeKey) {
            $err = "you can not use this route -($myKey) recursively";
            throw new LogicException($err);
        }
        else if (0 !== $pos = strpos($routeKey, $myKey)) {
            $err = "route -(key=$routeKey) must be a child of -(key=$myKey)";
            throw new LogicException($err);
        }
    
       return substr($routeKey, strlen($myKey) + 1);
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    protected function isValidString($key)
    {
         if (! is_string($key) || empty($key)) {
            return false;
        }
    
        return true;
    }
}
