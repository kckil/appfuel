<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use LogicException,
    OutOfBoundsException,
    InvalidArgumentException;

class ActionRoute implements ActionRouteInterface
{
    /**
     * @var string  $key
     */
    protected $key = null;

    /**
     * @return  string
     */
    protected $pattern = null;

    /**
     * Action controller used by the framework once a match is satified
     * @var string
     */
    protected $controller = null;

    /**
     * Action controller used when the all other matches fail. This is optional
     * @var string
     */
    protected $defaultController = null;

    /**  
     * Used to aid in generating urls for this route as well as naming
     * regex captures so you don't have to use (?<name>) syntax.
     * @var array
     */ 
    protected $params = array();

    /**
     * List of action routes
     * @var array
     */
    protected $routes = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct(array $spec)
    {
        if (! isset($spec['key'])) { 
            $err = '-(key) is expected but not given'; 
            throw new OutOfBoundsException($err); 
        } 
        $this->setKey($spec['key']);

        if (! isset($spec['pattern'])) { 
            $err = '-(pattern) regex pattern is expected but not given'; 
            throw new OutOfBoundsException($err); 
        } 
        $this->setPattern($spec['pattern']);

        if (! isset($spec['controller'])) { 
            $err = '-(controller) controller class is expected but not given'; 
            throw new OutOfBoundsException($err); 
        } 
        $this->setController($spec['controller']);

        if (isset($spec['params'])) {
            $this->setParams($spec['params']);
        }

        if (isset($spec['default-controller'])) {
            $this->setDefaultController($spec['default-controller']);
        }
    }

    /**
     * @return  string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return  string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return  string
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
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
     * @param  string  $key
     * @return  null
     */
    protected function setKey($key)
    {
        if (! $this->isValidString($key)) {
            $err = "route key must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->key = $key;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setPattern($pattern)
    {
        if (! $this->isValidString($pattern)) {
            $err = "uri pattern must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->pattern = $pattern;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setController($className)
    {
        if (! $this->isValidString($className)) {
            $err = "controller class must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->controller = $className;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setDefaultController($className)
    {
        if (! $this->isValidString($className)) {
            $err = "default controller class must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->defaultController = $className;
    }

    /**
     * @param   array   $params
     * @return  null
     */
    protected function setParams(array $params)
    {
        foreach ($params as $param) {
            if (! $this->isValidString($param)) {
                $err = 'route parameter must be a non empty string';
                throw new OutOfBoundsException($err);
            }
        }

        $this->params = $params;
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
