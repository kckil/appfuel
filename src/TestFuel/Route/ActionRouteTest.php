<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use Appfuel\Route\UriMatcher,
    Appfuel\Route\RouteSpec,
    Appfuel\Route\ActionRoute,
    Testfuel\FrameworkTestCase;

class ActionRouteTest extends FrameworkTestCase 
{

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function createRouteSpec(array $data)
    {
        return new RouteSpec($data);
    }

    /**
     * @param   array   $data
     * @return  UriMatcher
     */
    public function createUriMatcher(array $data)
    {
        return new UriMatcher($data);
    }

    /**
     * @return  string
     */
    public function getRouteSpecInterface()
    {
        return 'Appfuel\\Route\\RouteSpecInterface';
    }

    /**
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
     * @param   array $spec
     * @return  RouteMatcher
     */
    public function createActionRoute($spec)
    {
        return new ActionRoute($spec);
    }

    /**
     * @test
     * @return  ActionRoute
     */
    public function creatingActionRoute()
    {
        $spec = $this->createMockRouteSpec('sections');
        $route = $this->createActionRoute($spec);
        $interface = 'Appfuel\\Route\\ActionRouteInterface';
        $this->assertInstanceOf($interface, $route);
        $this->assertSame($spec, $route->getSpec());
    
        return $route;
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function addingARouteToItself()
    {
        $spec = $this->createMockRouteSpec();
        $spec->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('sections'));

        $route = $this->createActionRoute($spec);

        $msg = 'you can not use this route -(sections) recursively';
        $this->setExpectedException('LogicException', $msg);
        
        $route->add($route);
    }

    /**
     * @test
     * @depends         creatingActionRoute
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return  null
     */
    public function addingARouteInvalidRouteKey($badKey)
    {
        $spec = $this->createMockRouteSpec();
        $route = $this->createActionRoute($spec);
        
        $spec1 = $this->createMockRouteSpec();
        $spec1->expects($this->any())
              ->method('getKey')
              ->will($this->returnValue($badKey));

        $route1 = $this->createActionRoute($spec1);


        $msg = 'route key must be a non empty string';
        $this->setExpectedException('LogicException', $msg);
        
        $route->add($route1);
    }



    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function addingARouteWhereKeyIsNotAChild()
    {

        $spec = $this->createMockRouteSpec('route-a');
        $route = $this->createActionRoute($spec);

        $specA = $this->createMockRouteSpec('route-b.a');
        $routeA = $this->createActionRoute($specA);


        $msg = 'route -(key=route-b.a) must be a child of -(key=route-a)';
        $this->setExpectedException('LogicException', $msg);
        $route->add($routeA);
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  RouteAction
     */
    public function addingDirectRoutes()
    {
        $spec = $this->createMockRouteSpec('sections');
        $route = $this->createActionRoute($spec);
        
        $aKey = 'sections.section-a';
        $specA = $this->createMockRouteSpec($aKey);
        $sectionA = $this->createActionRoute($specA);

        $this->assertFalse($route->get($aKey));
        $this->assertSame($route, $route->add($sectionA));
        $this->assertSame($sectionA, $route->get($aKey));
        $this->assertSame($sectionA, $route->getDirect('section-a'));

        $bKey = 'sections.section-b';
        $specB = $this->createMockRouteSpec($bKey);
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
        $spec = $route->getSpec();
        $spec->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('sections'));

        $sectionA = $route->get('sections.section-a');
        $specA = $sectionA->getSpec();
        $specA->expects($this->any())
              ->method('getKey')
              ->will($this->returnValue('sections.section-a'));


        $axKey = 'sections.section-a.x';
        $specAX = $this->createMockRouteSpec($axKey);
        $routeAX = $this->createActionRoute($specAX);
        $this->assertFalse($route->get($axKey));
        $this->assertSame($route, $route->add($routeAX));
        $this->assertSame($routeAX, $route->get($axKey));

        $this->assertSame($sectionA, $sectionA->add($routeAX));
        $this->assertSame($routeAX, $sectionA->get($axKey));
        $this->assertSame($routeAX, $sectionA->getDirect('x'));

        $axyKey = 'sections.section-a.x.y';
        $specAXY = $this->createMockRouteSpec($axyKey);

        $routeAXY = $this->createActionRoute($specAXY);
        $this->assertSame($route, $route->add($routeAXY));

        $sectionAX = $route->get('sections.section-a.x');
        $this->assertSame($routeAXY, $route->get($axyKey));

        $this->assertSame($sectionAX, $sectionAX->add($routeAXY));
        $this->assertSame($routeAXY, $sectionAX->get($axyKey));
        $this->assertSame($routeAXY, $sectionAX->getDirect('y'));
    }

    /**
     * @test
     * @depends addingDirectRoutes
     * @return  null
     */
    public function tryingToGetARouteFromTheRoute(ActionRoute $route)
    {
        $spec = $route->getSpec();
        $spec->expects($this->any())
             ->method('getKey')
             ->will($this->returnValue('sections'));


        $msg = 'you can not use this route -(sections) recursively';
        $this->setExpectedException('LogicException', $msg);

        $route->get('sections');
    }

    /**
     * @test
     * @depends creatingActionRoute
     * @return  null
     */
    public function failedMatch()
    {
        $spec = $this->createRouteSpec(array(
            'key' => 'example',
            'pattern' => '#^/my-route$#',
        ));
        $route = $this->createActionRoute($spec);

        $matcher = $this->createUriMatcher(array(
            'uri-path' => '/myroute',
            'uri-scheme' => 'http',
            'http-method' => 'get'
        ));

        $this->assertFalse($route->match($matcher));
    }

    /**
     * @depends creatingActionRoute
     * @return  null
     */
    public function matching()
    {
        $spec = $this->createMockRouteSpec();
        $spec->expects($this->any())
             ->method('getPattern')
             ->will($this->returnValue('/^my-route$/'));

        $route = $this->createActionRoute($spec);
        
        $matched = $route->match('my-route');
        $class = 'Appfuel\\Route\\MatchedRoute';
        $this->assertInstanceOf($class, $matched);
    }

    /**
     * @depends creatingActionRoute
     * @return  null
     */
    public function matchingWithCaptures()
    {
        $spec = $this->createMockRouteSpec();
        $spec->expects($this->any())
             ->method('getPattern')
             ->will($this->returnValue('#^my-route/(\w+)/(\d+)#'));

        $spec->expects($this->any())
             ->method('getParams')
             ->will($this->returnValue(array('name', 'id')));

        $route = $this->createActionRoute($spec);
        
        $matched = $route->match('my-route/robert/12345');
        $class = 'Appfuel\\Route\\MatchedRoute';
        $this->assertInstanceOf($class, $matched);

        $this->assertSame($spec, $matched->getSpec());
    }

    /**
     * @depends creatingActionRoute
     * @return  null
     */
    public function matchingWithCapturesHierarchy()
    {
        $userData = array('key' => 'users', 'pattern' => '#^/users#');
        $userSpec = $this->createRouteSpec($userData);
        $users = $this->createActionRoute($userSpec);

        $groupData = array(
            'key' => 'users.group', 
            'pattern' => '#/group/([-_a-z]+)/(staff|admin|guest)#i',
            'params'  => array('group-category', 'group-type')
        );
        $groupSpec = $this->createRouteSpec($groupData);
        $userGroup = $this->createActionRoute($groupSpec);
        $users->add($userGroup);

        $rsbData = array(
            'key' => 'users.group.rsb',
            'pattern' => '#/rsb/(\d+)$#',
            'params'  => array('rsb-id'),
            'controller' => 'RsbController'
        );
        $rsbSpec = $this->createRouteSpec($rsbData);
        $rsb = $this->createActionRoute($rsbSpec);

        $users->add($rsb);
        $uri = '/users/group/cat-a/staff/rsb/12345';
        $matched = $users->match($uri);
        $this->assertInstanceOf('Appfuel\\Route\\MatchedRoute', $matched);
        $this->assertEquals($rsbSpec, $matched->getSpec());
        $expected = array(
            'group-category' => 'cat-a',
            'group-type'     => 'staff',
            'rsb-id'         => 12345,
        );
        $this->assertEquals($expected, $matched->getCaptures());
    }
}
