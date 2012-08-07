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
     * @return  RouteMatcher
     */
    public function createRouteSpec(array $spec)
    {
        return new RouteSpec($spec);
    }

    /**
     * @return  array
     */
    public function getDefaultData()
    {
        return array(
            'key' => 'sections',
            'pattern'   => '#^sections',
            'controller' => 'My\\Controller\\ControllerClass',
        );
    }

    /**
     * @test
     * @return  RouteSpec
     */
    public function creatingRouteSpec()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);

        $interface = 'Appfuel\\Route\\RouteSpecInterface';
        $this->assertInstanceOf($interface, $spec);

        $this->assertEquals($data['key'], $spec->getKey());
        $this->assertEquals($data['pattern'], $spec->getPattern());
        $this->assertEquals($data['controller'], $spec->getController());
        $this->assertEquals('execute', $spec->getControllerMethod());
        
        return $spec;
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function creatingRouteSpecClosureController()
    {
        $data = $this->getDefaultData();
        $data['controller'] = function() {
            return 'whatever';
        };

        $spec = $this->createRouteSpec($data);
        $this->assertEquals($data['controller'], $spec->getController());
        
        $data['controller'] = array($this, 'getDefaultData');
        $spec = $this->createRouteSpec($data);
        $this->assertEquals($data['controller'], $spec->getController());

        return $spec;
    }

    /**
     * @depends creatingRouteSpec
     * @return  ActionRoute
     */
    public function creatingRouteSpecClosureDefaultController()
    {
        $data = $this->getDefaultData();
        $data['default-controller'] = function() {
            return 'whatever';
        };

        $spec = $this->createRouteSpec($data);
        $this->assertEquals(
            $data['default-controller'], 
            $spec->getDefaultController()
        );
        
        $data['default-controller'] = array($this, 'getDefaultSpec');
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(
            $data['default-controller'], 
            $spec->getDefaultController()
        );

        return $spec;
    }

    /** 
     * @test
     * @depends creatingRouteSpec
     * @return  null 
     */
    public function creatingCollectionNoRouteKey() 
    { 
        $msg = '-(key) is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $data = $this->getDefaultData();
        unset($data['key']); 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidRouteKey($badKey) 
    { 
        $msg = 'route key must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['key'] = $badKey; 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends creatingRouteSpec
     * @return  null 
     */
    public function creatingCollectionNoPatternKey() 
    { 
        $msg = '-(pattern) regex pattern is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $data = $this->getDefaultData();
        unset($data['pattern']); 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidPattern($badPattern) 
    { 
        $msg = 'pattern must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['pattern'] = $badPattern; 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends creatingRouteSpec
     * @return  null 
     */
    public function creatingCollectionNoController() 
    { 
        $msg = '-(controller) controller class is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $data = $this->getDefaultData();
        unset($data['controller']); 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidController($badClassName) 
    { 
        $msg = 'controller must be callable or a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['controller'] = $badClassName; 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidRouteParams($badParam) 
    { 
        $msg = 'route parameter must be a non empty string'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $data = $this->getDefaultData();
        $data['params'] = array('name', $badParam, 'type'); 
        $spec = $this->createRouteSpec($data); 
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  ActionRoute
     */
    public function creatingRouteSpecWithParams()
    {
        $data = $this->getDefaultData();
        $data['params'] = array('name', 'type', 'id');
        $spec = $this->createRouteSpec($data);
        $this->assertEquals($data['key'], $spec->getKey());
        $this->assertEquals($data['pattern'], $spec->getPattern());
        $this->assertEquals($data['controller'], $spec->getController());
        $this->assertEquals($data['params'], $spec->getParams());

        return $spec;
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  ActionRoute
     */
    public function creatingRouteSpecWithControllerMethod()
    {
        $data = $this->getDefaultData();
        $data['controller-method'] = 'my_method';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(
            $data['controller-method'], 
            $spec->getControllerMethod()
        );

        return $spec;
    }

    /** 
     * @test
     * @depends         creatingRouteSpecWithControllerMethod
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingActionInvalidMethod($badName) 
    { 
        $msg = 'controller method name must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['controller-method'] = $badName; 
        $spec = $this->createRouteSpec($data); 
    }


    /**
     * @depends creatingRouteSpec
     * @return  ActionRoute
     */
    public function creatingRouteSpecWithDefaultController()
    {
        $spec = $this->getDefaultSpec();
        $spec['default-controller'] = 'My\\Default\\Controller';
        $spec = $this->createRouteSpec($spec);
        $this->assertEquals($spec['key'], $spec->getKey());
        $this->assertEquals($spec['pattern'], $spec->getPattern());
        $this->assertEquals($spec['controller'], $spec->getController());
        $this->assertEquals(array(), $spec->getParams());
        $this->assertEquals(
            $spec['default-controller'], 
            $spec->getDefaultController()
        );

        return $spec;
    }

    /** 
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingRouteSpecInvalidDefaultController($badClassName) 
    { 
        $msg = 'default controller must be callable or a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['default-controller'] = $badClassName; 
        $spec = $this->createRouteSpec($spec); 
    }
}
