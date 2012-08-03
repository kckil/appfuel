<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Route\RouteSpec,
    Testfuel\FrameworkTestCase;

class RouteSpecTest extends FrameworkTestCase 
{

    /**
     * @param   array $spec
     * @return  RouteSpec
     */
    public function createRouteSpec(array $spec)
    {
        return new RouteSpec($spec);
    }

    /**
     * @return  array
     */
    public function getRouteSpecData()
    {
        return array(
            'key'        => 'my-route',
            'pattern'    => '^path/to/route$',
            'controller' => 'My\\Controller\\ActionController'
        );
    }
    
    /**
     * @test
     * @return  array
     */
    public function creatingRouteSpec()
    {
        $spec = $this->getRouteSpecData();
        $route = $this->createRouteSpec($spec);
        $interface = 'Appfuel\\Route\\RouteSpecInterface';
        $this->assertInstanceOf($interface, $route);

        $this->assertEquals($spec['key'], $route->getKey());
        $this->assertEquals($spec['pattern'], $route->getPattern());
        $this->assertEquals($spec['controller'], $route->getController());
        $this->assertEquals(array(), $route->getParams());

        return $spec;
    }

    /**
     * @test
     * @return  array
     */
    public function creatingRouteSpecWithParams()
    {
        $spec = $this->getRouteSpecData();
        $spec['params'] = array('name', 'type');
        $route = $this->createRouteSpec($spec);
        $interface = 'Appfuel\\Route\\RouteSpecInterface';
        $this->assertInstanceOf($interface, $route);

        $this->assertEquals($spec['key'], $route->getKey());
        $this->assertEquals($spec['pattern'], $route->getPattern());
        $this->assertEquals($spec['controller'], $route->getController());
        $this->assertEquals($spec['params'], $route->getParams());

        return $spec;
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  null
     */
    public function creatingRouteSpecNoKey(array $spec)
    {
        $msg = '-(key) route key is expected but not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        unset($spec['key']);
        $spec = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function createRouteSpecBadKey($badKey)
    {
        $msg = 'route key must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $spec = $this->getRouteSpecData();
        $spec['key'] = $badKey;

        $route = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  null
     */
    public function creatingRouteNoPattern(array $spec)
    {
        $msg = '-(pattern) regex pattern is expected but not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        unset($spec['pattern']);
        $spec = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function createRouteSpecBadPattern($badPattern)
    {
        $msg = 'pattern must a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $spec = $this->getRouteSpecData();
        $spec['pattern'] = $badPattern;

        $route = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  null
     */
    public function creatingRouteNoController(array $spec)
    {
        $msg = '-(controller) the controller class is expected but not given';
        $this->setExpectedException('OutOfBoundsException', $msg);

        unset($spec['controller']);
        $spec = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function createRouteSpecBadController($badController)
    {
        $msg = 'controller class must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $spec = $this->getRouteSpecData();
        $spec['controller'] = $badController;

        $route = $this->createRouteSpec($spec);
    }

    /**
     * @test
     * @depends         creatingRouteSpecWithParams
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function createRouteSpecBadRouteParam($badParam)
    {
        $msg = 'route parameter must be a non empty string';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $spec = $this->getRouteSpecData();
        $spec['params'] = array('name', $badParam, 'type');

        $route = $this->createRouteSpec($spec);
    }



}
