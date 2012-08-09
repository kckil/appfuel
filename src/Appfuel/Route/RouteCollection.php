<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use OutOfBoundsException;

class RouteCollection implements RouteCollectionInterface
{
    /**
     * List of ActionRoutes each route can hold a tree of routes
     * @var array
     */
    private $routes = array();

    /**
     * @param   string  $key
     * @param   ActionRouteInterface    $routes
     * @return  RouteCollection
     */    
    public function add(ActionRouteInterface $route)
    {
        $key  = $route->getKey();
        if (false === strpos($key, '.')) {
            $this->routes[$key] = $route;
            return $this;
        }

        $root = $this->getRootKey($key);
        if (! isset($this->routes[$root])) {
            $err = "could not find the root action of this key -($key, $root)";
            throw new OutOfBoundsException($err);
        }

        $this->routes[$root]->add($route);
        return $this;
    }

    /**
     * @param   string  $key
     * @return  false | ActionRouteInterface
     */
    public function get($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "route key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (false === strpos($key, '.')) {
            if (! isset($this->routes[$key])) {
                return false;
            }

            return $this->routes[$key];
        }

        $root = $this->getRootKey($key);
        if (! isset($this->routes[$root])) {
            return false;
        }

        return $this->routes[$root]->get($key);
    }

    /**
     * @param   array   $data
     * @return  UriMatcher
     */
    public function createUriMatcher(array $data)
    {
        return new UriMatcher($data);
    }

    /**
     * @param   string  $key
     * @return  string
     */
    protected function getRootKey($key)
    {
        $parts = explode('.', $key);
        return $parts[0]; 
    }
}
