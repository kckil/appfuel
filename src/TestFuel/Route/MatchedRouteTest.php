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
     * @return  RouteSpecInterface
     */
    public function createMockRouteSpec()
    {
        $interface = 'Appfuel\\Route\\RouteSpecInterface';
        $mock = $this->getMock($interface);
        return $mock;
    }

    /**
     * @param   RouteSpecInterface $spec
     * @return  MatchedRoute
     */
    public function createMatchedRoute($spec, array $args=null)
    {
        return new MatchedRoute($spec, $args);
    }

    /**
     * @test
     * @return  MatchedRoute
     */
    public function creatingMatchedRoute()
    {
        $spec = $this->createMockRouteSpec();
        $matched = $this->createMatchedRoute($spec);
        $interface = 'Appfuel\\Route\\MatchedRouteInterface';
        $this->assertInstanceOf($interface, $matched);
        $this->assertSame($spec, $matched->getSpec());
        $this->assertEquals(array(), $matched->getCaptures());
    }

   /**
     * @test
     * @depends creatingMatchedRoute
     * @return  MatchedRoute
     */
    public function creatingMatchedRouteWithCaptures()
    {
        $spec = $this->createMockRouteSpec();
        $captures = array('name' => 'robert');
        $matched = $this->createMatchedRoute($spec, $captures);
        $this->assertEquals($captures, $matched->getCaptures());
    }
}
