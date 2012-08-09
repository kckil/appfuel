<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use Appfuel\Http\HttpResponse,
    Appfuel\Http\HttpRequestInterface,
    Appfuel\Http\HttpResponseInterface;

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
       
        $uriMatcher = $collection->createUriMatcher(array(
            'uri-path'    => $request->getPathInfo(),
            'uri-scheme'  => $request->getScheme(),
            'http-method' => $request->getMethod()
        ));

        $matched = $collection->matchUri($uriMatcher);
        if (! $matched instanceof MatchedRouteInterface) {
            return false;
        }

        $controller = $matched->createCallableController();
        $captures   = $matched->getCaptures();
        $response = call_user_func($controller, array_values($captures));

        if (is_string($response)) {
            $response = $this->createHttpResponse($response);
        }
        else if (! $response instanceof HttpResponseInterface) {
            $response = $this->createHttpResponse();
        }

        return $response;
    }

    /**
     * @param   string  $data
     * @param   int $status
     * @param   array   $headers
     * @return  HttpResponse
     */
    protected function createHttpResponse($data = null, 
                                          $status = null,
                                          $headers = null)
    {
        return new HttpResponse($data, $status, $headers);
    }
}
