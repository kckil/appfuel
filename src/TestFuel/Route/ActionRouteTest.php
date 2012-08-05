<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Route\ActionRoute,
    Testfuel\FrameworkTestCase;

class ActionRouteTest extends FrameworkTestCase 
{

    /**
     * @param   array $spec
     * @return  RouteMatcher
     */
    public function createActionRoute(array $spec)
    {
        return new ActionRoute($spec);
    }

    /**
     * @return  array
     */
    public function getDefaultSpec()
    {
        return array(
            'route-key' => 'sections',
            'pattern'   => '#^sections',
            'controller' => 'My\\Controller\\ControllerClass',
        );
    }

    /**
     * @test
     * @return  ActionRoute
     */
    public function creatingActionRoute()
    {
        $spec = $this->getDefaultSpec();
        $routes = $this->createActionRoute($spec);
        $interface = 'Appfuel\\Route\\ActionRouteInterface';
        $this->assertInstanceOf($interface, $routes);
        $this->assertEquals($spec['route-key'], $routes->getKey());
        $this->assertEquals($spec['pattern'], $routes->getPattern());
        $this->assertEquals($spec['controller'], $routes->getController());
        
 
        return $routes;
    }

    /** 
     * @test 
     * @depends creatingActionRoute
     * @return  null 
     */
    public function creatingCollectionNoRouteKey() 
    { 
        $msg = '-(route-key) is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $spec = $this->getDefaultSpec();
        unset($spec['route-key']); 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidRouteKey($badKey) 
    { 
        $msg = 'route key must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['route-key'] = $badKey; 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends creatingActionRoute
     * @return  null 
     */
    public function creatingCollectionNoPatternKey() 
    { 
        $msg = '-(pattern) regex pattern is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $spec = $this->getDefaultSpec();
        unset($spec['pattern']); 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidPattern($badPattern) 
    { 
        $msg = 'pattern must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['pattern'] = $badPattern; 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends creatingActionRoute
     * @return  null 
     */
    public function creatingCollectionNoController() 
    { 
        $msg = '-(controller) controller class is expected but not given'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $spec = $this->getDefaultSpec();
        unset($spec['controller']); 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidController($badClassName) 
    { 
        $msg = 'controller class must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['controller'] = $badClassName; 
        $routes = $this->createActionRoute($spec); 
    }

    /** 
     * @test 
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingCollectionInvalidRouteParams($badParam) 
    { 
        $msg = 'route parameter must be a non empty string'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['route-params'] = array('name', $badParam, 'type'); 
        $routes = $this->createActionRoute($spec); 
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  ActionRoute
     */
    public function creatingActionRouteWithParams()
    {
        $spec = $this->getDefaultSpec();
        $spec['route-params'] = array('name', 'type', 'id');
        $routes = $this->createActionRoute($spec);
        $this->assertEquals($spec['route-key'], $routes->getKey());
        $this->assertEquals($spec['pattern'], $routes->getPattern());
        $this->assertEquals($spec['controller'], $routes->getController());
        $this->assertEquals($spec['route-params'], $routes->getParams());

        return $routes;
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  ActionRoute
     */
    public function creatingActionRouteWithDefaultController()
    {
        $spec = $this->getDefaultSpec();
        $spec['default-controller'] = 'My\\Default\\Controller';
        $routes = $this->createActionRoute($spec);
        $this->assertEquals($spec['route-key'], $routes->getKey());
        $this->assertEquals($spec['pattern'], $routes->getPattern());
        $this->assertEquals($spec['controller'], $routes->getController());
        $this->assertEquals(array(), $routes->getParams());
        $this->assertEquals(
            $spec['default-controller'], 
            $routes->getDefaultController()
        );

        return $routes;
    }

    /** 
     * @test 
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingActionRouteInvalidDefaultController($badClassName) 
    { 
        $msg = 'default controller class must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $spec = $this->getDefaultSpec();
        $spec['default-controller'] = $badClassName; 
        $routes = $this->createActionRoute($spec); 
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function addingDirectRoutes(ActionRoute $route)
    {
        $specA = array(
            'route-key'     => 'sections.section-a',
            'pattern'       => '/^somepatter/',
            'controller'    => 'SectionAController',
        );
        $sectionA = $this->createActionRoute($specA);

        $this->assertFalse($route->exists('sections.section-a'));
        $this->assertSame($route, $route->add($sectionA));
        $this->assertTrue($route->exists('sections.section-a'));

        $specB = array(
            'route-key'     => 'sections.section-b',
            'pattern'       => '/^otherpattern/',
            'controller'    => 'SectionBController',
        );
        $sectionB = $this->createActionRoute($specB);

        $this->assertFalse($route->exists('sections.section-b'));
        $this->assertSame($route, $route->add($sectionB));
        $this->assertTrue($route->exists('sections.section-b'));

    }


}
