<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use Appfuel\Route\MatchedRoute;

class MatchedRouteTest extends TestRouteCase 
{

    /**
     * @test
     * @return  MatchedRoute
     */
    public function creatingMatchedRoute()
    {
        $spec = $this->createMockRouteSpec();
        $matched = $this->createMatchedRoute($spec);
        
        $interface = $this->getMatchedRouteInterface();
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
