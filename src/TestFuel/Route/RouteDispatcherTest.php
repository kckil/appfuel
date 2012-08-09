<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use StdClass,
    Appfuel\Route\RouteDispatcher,
    Appfuel\Route\RouteCollection,
    Appfuel\Route\RouteCollectionInterface;

require_once __DIR__ . '/Fixtures/Controller/MyController.php';

class RouteDispatcherTest extends TestRouteCase 
{

    /**
     * @return  \Appfuel\Http\HttpRequest
     */
    public function createRequestDefaultData()
    {
        return $this->createHttpRequest(array(
            'REQUEST_URI'     => '/users',
            'REQUEST_METHOD'  => 'GET',
            'HTTPS'           => 'on',
            'SCRIPT_FILENAME' => '/some/root/www/index-dev.php',
        ));
    }

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function createRouteDispatcher(RouteCollectionInterface $collection)
    {
        return new RouteDispatcher($collection);
    }

    public function getUriMatcherData($path = null)
    {
        $default = '/users/user-a/12345/type/admin';
        if (null === $path) {
            $path = $default;
        }

        return array(
            'uri-path'    => $path,
            'uri-scheme'  => 'http',
            'http-method' => 'get'
        );
    }

    /**
     * @test
     * @return  RouteDispatcher
     */
    public function creatingRouteDispatcher()
    {
        $col = $this->getMock('Appfuel\\Route\\RouteCollectionInterface');
        $dispatcher = $this->createRouteDispatcher($col);
        $interface = 'Appfuel\\Route\\RouteDispatcherInterface';
        $this->assertInstanceOf($interface, $dispatcher);
        $this->assertSame($col, $dispatcher->getRouteCollection());

        return $dispatcher;
    }

    /**
     * @test
     * @depends creatingRouteDispatcher
     * @return  RouteDispatcher
     */
    public function dispatchingAMatchingUriControllerIsAClosure()
    {
        $collection = $this->createRouteCollection();
        $route = $this->createActionRouteWithSpecArray(array(
            'key'         => 'users',
            'pattern'     => '#^/users$#',
            'http-method' => 'get',
            'uri-scheme'  => 'https',
            'controller'  => function() {
                return 'hello world';
            },
        ));
        $collection->add($route);
        $dispatcher = $this->createRouteDispatcher($collection);

        $request = $this->createRequestDefaultData();

        $response = $dispatcher->dispatchHttpRequest($request);
        $class  = 'Appfuel\\Http\\HttpResponse';
        $this->assertInstanceOf($class, $response);
        $this->assertEquals('hello world', $response->getContent());
    }

    /**
     * @test
     * @depends creatingRouteDispatcher
     * @return  RouteDispatcher
     */
    public function dispatchingAMatchingUriControllerIsAClass()
    {
        $collection = $this->createRouteCollection();
        $route = $this->createActionRouteWithSpecArray(array(
            'key'         => 'users',
            'pattern'     => '#^/users$#',
            'http-method' => 'get',
            'uri-scheme'  => 'https',
            'controller'  => 'Testfuel\\Route\\Fixtures\\Controller\\MyController',
        ));
        $collection->add($route);
        $dispatcher = $this->createRouteDispatcher($collection);

        $request = $this->createRequestDefaultData();

        $response = $dispatcher->dispatchHttpRequest($request);
        $class  = 'Appfuel\\Http\\HttpResponse';
        $this->assertInstanceOf($class, $response);
        $this->assertEquals('goodbye world', $response->getContent());
    }
}
