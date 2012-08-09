<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Route;

use StdClass,
    Appfuel\Route\RouteCollectionBuilder;

class RouteCollectionBuilderTest extends TestRouteCase 
{

    /**
     * @return  array
     */
    public function provideValidRegexs()
    {
        return array(
            array('/^$/'),
            array('/users/i'),
            array('#^/users/(\w+)/(\d+)$#is')
        );
    }

    /**
     * @return  array
     */
    public function provideValidRawRegexData()
    {
        return array(
            array('', '##'),
            array('#', '#\\##'),
            array('^$', '#^$#'),
            array('/users', '#/users#'),
            array('/users/(\w+)$', '#/users/(\w+)$#'),
            array('\\', '#\\\\\#'),
            array(array('', 'i'), '##i'),
            array(array('users', 'is'), '#users#is'),
            array(
                array('users/(?:staff|admin)/(\w+)', 'is'), 
                '#users/(?:staff|admin)/(\w+)#is'
            ),
        );
    }

    /**
     * @test
     * @return  RouteCollectionBuilder
     */
    public function creatingRouteCollectionBuilder()
    {
        $builder = $this->createRouteCollectionBuilder();
        $interface = $this->getRouteCollectionBuilderInterface();
        $this->assertInstanceOf($interface, $builder);

        return $builder;
    }

    /**
     * @test
     * @depends         creatingRouteCollectionBuilder
     * @dataProvider    provideValidRawRegexData
     */
    public function compileRegexString($raw, $expected)
    {
        $builder = $this->createRouteCollectionBuilder();
        $this->assertEquals($expected, $builder->compileRegex($raw));
    }

    /**
     * @test
     * @depends creatingRouteCollectionBuilder
     */
    public function compileRegexNoValidParam(RouteCollectionBuilder $builder)
    {
        $msg  = 'raw regex must be a string or an array where the first item ';
        $msg .= 'is the regex and the second is a string of pattern modifiers';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $builder->compileRegex(new StdClass);
    }

    /**
     * @test
     * @depends creatingRouteCollectionBuilder
     */
    public function compileRegexInvalidArray1(RouteCollectionBuilder $builder)
    {
        $msg = 'both items in the regex array must be valid strings';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $raw = array(new StdClass, 'i');
        $builder->compileRegex($raw);
    }

    /**
     * @test
     * @depends creatingRouteCollectionBuilder
     */
    public function compileRegexInvalidArray2(RouteCollectionBuilder $builder)
    {
        $msg = 'both items in the regex array must be valid strings';
        $this->setExpectedException('OutOfBoundsException', $msg);

        $raw = array('^/users$', new StdClass);
        $builder->compileRegex($raw);
    }

    /**
     * @test
     * @depends         creatingRouteCollectionBuilder
     * @dataProvider    provideValidRegexs
     */
    public function validateRegex($regex)
    {
        $builder = $this->createRouteCollectionBuilder();
        $this->assertTrue($builder->validateRegex($regex));
    }

    /**
     * @test
     * @depends creatingRouteCollectionBuilder
     */
    public function validateBadRegex(RouteCollectionBuilder $builder)
    {
        $result = $builder->validateRegex('');
        $this->assertEquals('preg_match(): Empty regular expression', $result);

        $result = $builder->validateRegex('/^$');
        $expected = "preg_match(): No ending delimiter '/' found";
        $this->assertEquals($expected, $result);
        
        $result = $builder->validateRegex('###');
        $expected = "preg_match(): Unknown modifier '#'";
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @depends creatingRouteCollectionBuilder
     * @return  
     */
    public function creatingARouteCollection(RouteCollectionBuilder $builder)
    {
        $spec1 = array('key' => 'users', 'pattern' => '/');
        $spec2 = array('key' => 'users.groups', 'pattern' => '/');
        $spec3 = array('key' => 'users.groups.a', 'pattern' => '/');
        $spec4 = array('key' => 'users.groups.b', 'pattern' => '/');
        $spec5 = array('key' => 'users.groups.c', 'pattern' => '/');
        $spec6 = array('key' => 'projects', 'pattern' => '/');
        $spec7 = array('key' => 'projects.a', 'pattern' => '/');
        $spec8 = array('key' => 'projects.b', 'pattern' => '/');
        $list = array($spec5, $spec7, $spec3, $spec1, $spec2, $spec4,$spec6,
                    $spec8);
        
        $collection = $builder->createRouteCollection($list);
        $class = 'Appfuel\\Route\\RouteCollection';
        $this->assertInstanceOf($class, $collection);
        
        $user = $collection->get('users');
        $class = 'Appfuel\\Route\\ActionRoute';
        $this->assertInstanceOf($class, $user);
        $this->assertEquals('users', $user->getKey());
        
        $groups = $user->get('users.groups');
        $this->assertInstanceOf($class, $groups);
        $this->assertEquals('users.groups', $groups->getKey());
        
        $groupA = $groups->get('users.groups.a');
        $this->assertInstanceOf($class, $groupA);
        $this->assertEquals('users.groups.a', $groupA->getKey());

        $groupB = $groups->get('users.groups.b');
        $this->assertInstanceOf($class, $groupB);
        $this->assertEquals('users.groups.b', $groupB->getKey());

        $groupC = $groups->get('users.groups.c');
        $this->assertInstanceOf($class, $groupC);
        $this->assertEquals('users.groups.c', $groupC->getKey());

        
        $projects = $collection->get('projects');
        $this->assertInstanceOf($class, $projects);
        $this->assertEquals('projects', $projects->getKey());
 
        $prjA = $projects->get('projects.a');
        $this->assertInstanceOf($class, $prjA);
        $this->assertEquals('projects.a', $prjA->getKey());

        $prjB = $projects->get('projects.b');
        $this->assertInstanceOf($class, $prjB);
        $this->assertEquals('projects.b', $prjB->getKey());
    }
}
