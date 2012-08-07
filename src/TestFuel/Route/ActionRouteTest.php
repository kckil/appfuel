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
            'key' => 'sections',
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
        $this->assertEquals($spec['key'], $routes->getKey());
        $this->assertEquals($spec['pattern'], $routes->getPattern());
        $this->assertEquals($spec['controller'], $routes->getController());
        $this->assertEquals('execute', $routes->getControllerMethod());
        
        return $routes;
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function AddingARouteToItself()
    {

        $spec = $this->getDefaultSpec();
        $route = $this->createActionRoute($spec);

        $msg = 'you can not use this route -(sections) recursively';
        $this->setExpectedException('LogicException', $msg);
        
        $route->add($route);
    }


    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function AddingARouteWhereKeyIsNotAChild()
    {

        $spec = $this->getDefaultSpec();
        $route = $this->createActionRoute($spec);

        $specA = array(
            'key' => 'not-sections.a',
            'pattern'   => '/blah/',
            'controller' => 'someController'
        );
        $routeA = $this->createActionRoute($specA);


        $msg = 'route -(key=not-sections.a) must be a child of -(key=sections)';
        $this->setExpectedException('LogicException', $msg);
        $route->add($routeA);
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  RouteAction
     */
    public function addingDirectRoutes(ActionRoute $route)
    {
        $aKey = 'sections.section-a';
        $specA = array(
            'key'     => $aKey,
            'pattern'       => '/^somepatter/',
            'controller'    => 'SectionAController',
        );
        $sectionA = $this->createActionRoute($specA);

        $this->assertFalse($route->get($aKey));
        $this->assertSame($route, $route->add($sectionA));
        $this->assertSame($sectionA, $route->get($aKey));
        $this->assertSame($sectionA, $route->getDirect('section-a'));

        $bKey = 'sections.section-b';
        $specB = array(
            'key'       => $bKey,
            'pattern'   => '/^otherpattern/',
            'controller'=> 'SectionBController',
        );
        $sectionB = $this->createActionRoute($specB);

        $this->assertFalse($route->get($bKey));
        $this->assertSame($route, $route->add($sectionB));
        $this->assertSame($sectionB, $route->get($bKey));
        $this->assertSame($sectionB, $route->getDirect('section-b'));


        $this->assertFalse($route->getDirect('section-not-there'));

        /* getDirect does not validate the relative position of the key
         * it will simply return false
         */
        $this->assertFalse($route->getDirect('sections'));
        return $route;
    }

    /**
     * @test
     * @depends addingDirectRoutes
     * @return  null
     */
    public function addingAChainOfRoutes(ActionRoute $route)
    {
        $axKey = 'sections.section-a.x';
        $specAX = array(
            'key'       => $axKey,
            'pattern'   => '/^xpattern/',
            'controller'=> 'XController',
        );
        $routeAX = $this->createActionRoute($specAX);
        $this->assertFalse($route->get($axKey));
        $this->assertSame($route, $route->add($routeAX));
        $this->assertSame($routeAX, $route->get($axKey));

        $sectionA = $route->get('sections.section-a');
        $this->assertSame($sectionA, $sectionA->add($routeAX));
        $this->assertSame($routeAX, $sectionA->get($axKey));
        $this->assertSame($routeAX, $sectionA->getDirect('x'));

        $specAXY = array(
            'key'       => 'sections.section-a.x.y',
            'pattern'   => '/^xypattern/',
            'controller'=> 'XYController',
        );
        $routeAXY = $this->createActionRoute($specAXY);
        $this->assertSame($route, $route->add($routeAXY));

        $sectionAX = $route->get('sections.section-a.x');
        $this->assertSame($routeAXY, $route->get('sections.section-a.x.y'));

        $this->assertSame($sectionAX, $sectionAX->add($routeAXY));
        $this->assertSame($routeAXY, $sectionAX->get('sections.section-a.x.y'));
        $this->assertSame($routeAXY, $sectionAX->getDirect('y'));
    }

    /**
     * @test
     * @depends addingDirectRoutes
     * @return  null
     */
    public function tryingToGetARouteFromTheRoute(ActionRoute $route)
    {
        $msg = 'you can not use this route -(sections) recursively';
        $this->setExpectedException('LogicException', $msg);

        $route->get('sections');
    }

    /**
     * @test
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null
     */
    public function matchInvalidPathFailure($badPath)
    {
        $spec = $this->getDefaultSpec();
        $route = $this->createActionRoute($spec);

        $msg = 'uri path must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $result = $route->match($badPath);
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function failedMatch()
    {
        $spec = $this->getDefaultSpec();
        $spec['pattern'] = '/^my-route$/';
        $route = $this->createActionRoute($spec);

        $this->assertFalse($route->match('myroute'));
        $this->assertFalse($route->match('/'));
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function matching()
    {
        $spec = $this->getDefaultSpec();
        $spec['pattern'] = '/^my-route$/';
        $route = $this->createActionRoute($spec);
        
        $matched = $route->match('my-route');
        $class = 'Appfuel\\Route\\MatchedRoute';
        $this->assertInstanceOf($class, $matched);
        $this->assertEquals($route->getKey(), $matched->getKey());
        $this->assertEquals($route->getController(), $matched->getController());
        $this->assertEquals(array(), $matched->getCaptures());
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function matchingWithCaptures()
    {
        $spec = $this->getDefaultSpec();
        $spec['pattern'] = '#^my-route/(\w+)/(\d+)#';
        $spec['params'] = array('name', 'id');

        $route = $this->createActionRoute($spec);
        
        $matched = $route->match('my-route/robert/12345');
        $class = 'Appfuel\\Route\\MatchedRoute';
        $this->assertInstanceOf($class, $matched);

        $this->assertEquals($route->getKey(), $matched->getKey());
        $this->assertEquals($route->getController(), $matched->getController());
        
        $expected = array(
            'name' => 'robert',
            'id' => '12345'
        );
        $this->assertEquals($expected, $matched->getCaptures());

        $func = function () {
            return 'blah';
        };
    }



}
