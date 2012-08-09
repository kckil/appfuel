<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException,
    InvalidArgumentException,
    Appfuel\Http\HttpResponse,
    Appfuel\Http\HttpRequestInterface,
    Appfuel\Http\HttpResponseInterface,
    Appfuel\Route\MatchedRouteInterface,
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

    /**
     * @param   HttpRequestInterface
     * @return  mixed
     */
    public function dispatchHttpRequest(HttpRequestInterface $request)
    {
        $collection = $this->getRouteCollection();
        $path = $request->getPathInfo();
       
        $uriMatcher = $this->createUriMatcher($request);
        $matched = $collection->match($uriMatcher);
        if (! $route instanceof MatchedRouteInterface) {
            return false;
        }

        $controller = $matched->createCallableController();
        $captures   = $match->getCaptures();
        $response = call_user_func($controller, array_values($captures));

        if (is_string($response)) {
            $response = $this->createHttpResponse($response);
        }
        else if (! $response instanceof HttpResponseInterface) {
            $response = $this->createHttpResponse();
        }

        return $response;
    }
}
