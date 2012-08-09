<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Route\UriMatcher,
    Appfuel\Route\RouteSpec,
    Appfuel\Route\ActionRoute,
    Appfuel\Route\MatchedRoute,
    Appfuel\Route\RouteCollection,
    Appfuel\Route\RouteSpecInterface,
    Appfuel\Route\ActionRouteInterface,
    Appfuel\Route\RouteCollectionBuilder,
    Appfuel\Route\RouteCollectionInterface,
    Appfuel\Route\RouteCollectionBuilderInterface;

class TestRouteCase extends FrameworkTestCase 
{
    /**
     * @var string
     */
    protected $routeNs = 'Appfuel\\Route';

    /**
     * @param   array $data
     * @return  ActionRouteSpec
     */
    public function createRouteSpec(array $data)
    {
        return new RouteSpec($data);
    }

    /**
     * @return string
     */
    public function getRouteSpecInterface()
    {
        return "{$this->routeNs}\\RouteSpecInterface";
    }

    /**
     * @param   string  $key
     * @return  RouteSpecInterface
     */
    public function createMockRouteSpec($key = null)
    {
        if (null === $key) { 
            return $this->getMock($this->getRouteSpecInterface()); 
        } 

        $spec = $this->getMock($this->getRouteSpecInterface()); 
        $spec->expects($this->any()) 
             ->method('getKey') 
             ->will($this->returnValue($key)); 
        
        return $spec; 
    }

    /**
     * @param   array $data
     * @return  ActionRouteSpec
     */
    public function createUriMatcher(array $data)
    {
        return new UriMatcher($data);
    }

    /**
     * @return string
     */
    public function getUriMatcherInterface()
    {
        return "{$this->routeNs}\\UriMatcherInterface";
    }

    /**
     * @param   RouteSpecInterface  $spec
     * @return  ActionRoute
     */
    public function createActionRoute(RouteSpecInterface $spec)
    {
        return new ActionRoute($spec);
    }

    /**
     * @param   array   $data
     * @return  ActionRoute
     */
    public function createActionRouteWithSpecArray(array $data)
    {
        return $this->createActionRoute($this->createRouteSpec($data));
    }

    /**
     * @return  string
     */
    public function getActionRouteInterface()
    {
        return "{$this->routeNs}\\ActionRouteInterface";
    }

    /**
     * @param   RouteSpecInterface  $s  route specification
     * @param   array               $c  regex captures
     * @return  MatchedRoute
     */
    public function createMatchedRoute(RouteSpecInterface $s, array $c = null)
    {
        return new MatchedRoute($s, $c);
    }

    /**
     * @return  string
     */
    public function getMatchedRouteInterface()
    {
        return "{$this->routeNs}\\MatchedRouteInterface";
    }

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function createRouteCollection()
    {
        return new RouteCollection(); 
    }

    /**
     * @return  RouteCollectionBuilder
     */
    public function createRouteCollectionBuilder()
    {
        return new RouteCollectionBuilder();
    }

    /**
     * @return string
     */
    public function getRouteCollectionBuilderInterface()
    {
        return "{$this->routeNs}\\RouteCollectionBuilderInterface";
    }
}
