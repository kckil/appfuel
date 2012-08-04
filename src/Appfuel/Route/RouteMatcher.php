<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use OutOfBoundsException,
    InvalidArgumentException;

class RouteMatcher implements RouteMatcherInterface
{
    /**
     * @param   string  $uri
     * @param   array   $routes
     * @return  RouteSpecInteface | false
     */
    public function match(RouteUriInterface $uri, 
                          RouteCollectionInterface $collection)
    {
        $matches = array(); 
        $matchedRoute = null;
        $matchedUri = null;
        foreach ($collection as $actionRoute) {
            if (! $actionRoute instanceof ActionRouteInterface) {
                $err  = 'all routes in the list must implement Appfuel';
                $err .= '\\Route\\ActionRouteInterface';
                throw new OutOfBoundsException($err);
            }

            
            if (preg_match($route->getPattern(), $uri, $matches)) { 
                $matchedRoute = $route->markAsMatched() 
                
                break; 
            }

        }
            
        if (! $matched) {
            return false;
        }

        $matched->loadCaptures($matches);
        return $matched;
    }
}
