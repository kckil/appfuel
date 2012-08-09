<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Route\RouteSpec,
    Appfuel\Route\ActionRoute,
    Appfuel\Route\RouteCollection;

class RouteCollectionTest extends FrameworkTestCase 
{

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function createActionRoute(array $data)
    {
        return new ActionRoute(new RouteSpec($data));
    }

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function createRouteCollection()
    {
        return new RouteCollection(); 
    }

    public function getUriMatcherData($path = null)
    {
        $default = 'users/user-a/12345/type/admin';
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
        $route1 = $this->createActionRoute(array(
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
        $route1 = $this->createActionRoute(array(
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
    public function addingChildOfExistantParent(RouteCollection $collection)
    {
        $route1 = $this->createActionRoute(array(
            'key' => 'users.user-a', 
            'pattern' => '#^/user-a/(\d+)#', 
            'controller' => 'UserAController'
        ));
        $this->assertSame($collection, $collection->add($route1));
        $this->assertSame($route1, $collection->get('users.user-a'));
 
        $route2 = $this->createActionRoute(array(
            'key' => 'users.user-a.type', 
            'pattern' => '#^/type/(\w+)#', 
            'controller' => 'TypeController'
        ));
        $this->assertSame($collection, $collection->add($route2));
        $this->assertSame($route2, $collection->get('users.user-a.type'));
        
        return $collection;
    }
}
