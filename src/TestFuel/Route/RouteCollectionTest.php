<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use StdClass,
    Appfuel\Route\RouteCollection;

class RouteCollectionTest extends TestRouteCase
{

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
     * @return  RouteCollection
     */
    public function creatingRouteCollection()
    {
        $collection = $this->createRouteCollection();
        $interface = 'Appfuel\\Route\\RouteCollectionInterface';
        $this->assertInstanceOf($interface, $collection);

        return $collection;
    }

    /**
     * @test
     * @depends creatingRouteCollection
     * @return  RouteCollection
     */
    public function creatingUriMatcher(RouteCollection $collection)
    {
        $data = $this->getUriMatcherData();
        $class = 'Appfuel\\Route\\UriMatcher';
        $matcher = $collection->createUriMatcher($data);
        $this->assertInstanceOf($class, $matcher);

        return $collection;
    }

    /**
     * @test
     * @depends creatingRouteCollection
     * @return  RouteCollection
     */
    public function addingParentRoute(RouteCollection $collection)
    {
        $route1 = $this->createActionRouteWithSpecArray(array(
            'key' => 'users', 
            'pattern' => '#^/users#', 
            'controller' => 'MyUserController'
        ));
        $this->assertSame($collection, $collection->add($route1));
        $this->assertSame($route1, $collection->get('users'));

        return $collection;
    }

    /**
     * @test
     * @depends addingParentRoute
     * @return  RouteCollection
     */
    public function addingChildOfNonExistantParent(RouteCollection $collection)
    {
        $route1 = $this->createActionRouteWithSpecArray(array(
            'key' => 'projects.system', 
            'pattern' => '#^/system/(\d+)#', 
            'controller' => 'ProjectSystemController'
        ));

        $msg  = 'could not find the root action of this key ';
        $msg .= '-(projects.system, projects)';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $collection->add($route1);
    }

    /**
     * @test
     * @depends addingParentRoute
     * @return  RouteCollection
     */
    public function addingChildOfExistingParent(RouteCollection $collection)
    {
        $route1 = $this->createActionRouteWithSpecArray(array(
            'key' => 'users.user-a', 
            'pattern' => '#^/user-a/(\d+)#', 
            'controller' => 'UserAController'
        ));
        $this->assertSame($collection, $collection->add($route1));
        $this->assertSame($route1, $collection->get('users.user-a'));
 
        $route2 = $this->createActionRouteWithSpecArray(array(
            'key' => 'users.user-a.type', 
            'pattern' => '#^/type/(\w+)#', 
            'controller' => 'TypeController',
            'params' => array('name')
        ));
        $this->assertSame($collection, $collection->add($route2));
        $this->assertSame($route2, $collection->get('users.user-a.type'));
        
        return $collection;
    }

    /**
     * @test
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @depends         creatingRouteCollection
     */
    public function gettingARouteWithAnInvalidKey($bad)
    {
        $msg = 'route key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $collection = $this->createRouteCollection();
        $collection->get($bad);
    }

    /**
     * @test
     * @depends addingChildOfExistingParent
     * @return  RouteCollection
     */
    public function matchingUri(RouteCollection $collection)
    {
        $data = $this->getUriMatcherData();
        $matcher = $collection->createUriMatcher($data);

        $matched = $collection->matchUri($matcher);
        $class = 'Appfuel\\Route\\MatchedRoute';
        $this->assertInstanceOf($class, $matched);
        $this->assertEquals('users.user-a.type', $matched->getKey());

        $data = $this->getUriMatcherData('/projects/12345');
        $matcher = $collection->createUriMatcher($data);
        $this->assertFalse($collection->matchUri($matcher));

        return $collection;
    }
}