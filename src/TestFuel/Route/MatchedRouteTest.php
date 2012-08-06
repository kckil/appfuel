<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Route\MatchedRoute,
    Testfuel\FrameworkTestCase;

class MatchedRouteTest extends FrameworkTestCase 
{

    /**
     * @param   array $spec
     * @return  RouteMatcher
     */
    public function createMatchedRoute($key, $ctrl, $method, array $args=null)
    {
        return new MatchedRoute($key, $ctrl, $method, $args);
    }

    /**
     * @test
     * @return  MatchedRoute
     */
    public function creatingMatchedRoute()
    {
        $key = 'my-key';
        $controller = 'MyController';
        $method = 'execute';
        $matched = $this->createMatchedRoute($key, $controller, $method);
        $interface = 'Appfuel\\Route\\MatchedRouteInterface';
        $this->assertInstanceOf($interface, $matched);
        $this->assertEquals($key, $matched->getKey());
        $this->assertEquals($method, $matched->getMethod());
        $this->assertEquals($controller, $matched->getController());
    }

    /** 
     * @test
     * @depends         creatingMatchedRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidRouteKey($badKey) 
    { 
      
        $msg = 'route key must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $matched = $this->createMatchedRoute($badKey, 'crtl', 'method');
    }

    /** 
     * @test
     * @depends         creatingMatchedRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidMethods($badName) 
    { 
        $msg = 'controller method must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $matched = $this->createMatchedRoute('my-key', 'ctrl', $badName);
    }

   /**
     * @test
     * @depends creatingMatchedRoute
     * @return  MatchedRoute
     */
    public function creatingMatchedRouteWithCaptures()
    {
        $key = 'my-key';
        $ctrl = 'MyController';
        $method = 'execute';
        $captures = array('name' => 'robert');
        $matched = $this->createMatchedRoute($key, $ctrl, $method, $captures);
        $this->assertEquals($captures, $matched->getCaptures());
    }
}
