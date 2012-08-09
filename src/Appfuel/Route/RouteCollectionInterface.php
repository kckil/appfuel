<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

interface RouteCollectionInterface
{
    /**
     * @param   string  $key
     * @param   ActionRouteInterface    $routes
     * @return  RouteCollection
     */    
    public function add(ActionRouteInterface $route);

    /**
     * @param   string  $key
     * @return  false | ActionRouteInterface
     */
    public function get($key);
}
