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
    public function createMatchedRoute($key, $ctrl, array $captures = null)
    {
        return new MatchedRoute($key, $ctrl, $captures);
    }

    /**
     * @test
     * @return  MatchedRoute
     */
    public function creatingMatchedRoute()
    {
        $key = 'my-key';
        $controller = 'MyController';
        $matched = $this->createMatchedRoute($key, $controller);
        $interface = 'Appfuel\\Route\\MatchedRouteInterface';
        $this->assertInstanceOf($interface, $matched);
        $this->assertEquals($key, $matched->getKey());
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

        $controller = 'MyController';
        $matched = $this->createMatchedRoute($badKey, $controller);
    }

    /** 
     * @test
     * @depends         creatingMatchedRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidController($badClassName) 
    { 
        $msg = 'controller class must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $matched = $this->createMatchedRoute('my-key', $badClassName);
    }

   /**
     * @test
     * @depends creatingMatchedRoute
     * @return  MatchedRoute
     */
    public function creatingMatchedRouteWithCaptures()
    {
        $key = 'my-key';
        $controller = 'MyController';
        $captures = array('name' => 'robert');
        $matched = $this->createMatchedRoute($key, $controller, $captures);
        $this->assertEquals($captures, $matched->getCaptures());
    }
}
