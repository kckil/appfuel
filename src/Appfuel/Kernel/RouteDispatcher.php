<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException,
    InvalidArgumentException,
    Appfuel\Route\RouteCollectionInterface;

class RouteDispatcher implements RouteDispatcherInterface
{
    /**
     * Holds a list of routes
     * @var RouteCollectionInterface
     */
    protected $collection = null;

    /**
     * @param   RouteCollectionInterface $collection
     * @return  RouteDispatcher
     */
    public function __construct(RouteCollectionInterface $collection)
    {
        $this->collection = $collection;    
    }

    /**
     * @return  RouteCollection
     */
    public function getRouteCollection()
    {
        return $this->collection;
    }
}
