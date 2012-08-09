<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use Appfuel\Http\HttpRequestInterface;

interface RouteDispatcherInterface
{
    /**
     * @return  RouteCollection
     */
    public function getRouteCollection();

    /**
     * @param   HttpRequestInterface $request
     * @return  HttpResponseInterface
     */
    public function dispatchHttpRequest(HttpRequestInterface $request);
}
