<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Appfuel\Route\RouteSpec,
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
    public function creatingRouteSpecWithHttpMethod()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(array(), $spec->getHttpMethod());
      
        $data = $this->getDefaultData();
        $data['http-method'] = 'GET';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(array('GET'), $spec->getHttpMethod());

        /* gets converted to uppercase */ 
        $data = $this->getDefaultData();
        $data['http-method'] = 'get';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(array('get'), $spec->getHttpMethod());

        /* only checks its a valid string */
        $data = $this->getDefaultData();
        $data['http-method'] = 'x-method';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(array('x-method'), $spec->getHttpMethod());
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function creatingRouteSpecWithManyHttpMethodsString()
    {
        $data = $this->getDefaultData();
        $data['http-method'] = 'GET|POST|PUT|DELETE';
        $spec = $this->createRouteSpec($data);

        $expected = array('GET', 'POST', 'PUT', 'DELETE');
        $this->assertEquals($expected, $spec->getHttpMethod());
        foreach($expected as $method) {
            $this->assertTrue($spec->isHttpMethodAllowed($method));
        }
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function creatingRouteSpecWithManyHttpMethodsArray()
    {
        $data = $this->getDefaultData();
        $data['http-method'] = array('get', 'post', 'put', 'delete');
        $spec = $this->createRouteSpec($data);

        $expected = array('get', 'post', 'put', 'delete');
        $this->assertEquals($expected, $spec->getHttpMethod());
        foreach($expected as $method) {
            $this->assertTrue($spec->isHttpMethodAllowed($method));
        }
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function isHttpMethodAllowedNoMethodDeclared()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);
        $this->assertEquals(array(), $spec->getHttpMethod());
        
        $this->assertTrue($spec->isHttpMethodAllowed()); 
        $this->assertTrue($spec->isHttpMethodAllowed('get'));
        $this->assertTrue($spec->isHttpMethodAllowed('put'));
        $this->assertTrue($spec->isHttpMethodAllowed('post'));
        $this->assertTrue($spec->isHttpMethodAllowed('delete'));
        $this->assertTrue($spec->isHttpMethodAllowed('anything'));
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function isHttpMethodAllowedOneMethodDeclared()
    {
        $data = $this->getDefaultData();
        $data['http-method'] = 'get';
        $spec = $this->createRouteSpec($data);
        
        $this->assertTrue($spec->isHttpMethodAllowed('get'));
        $this->assertTrue($spec->isHttpMethodAllowed('GET'));
        $this->assertFalse($spec->isHttpMethodAllowed()); 
        $this->assertFalse($spec->isHttpMethodAllowed('put'));
        $this->assertFalse($spec->isHttpMethodAllowed('post'));
        $this->assertFalse($spec->isHttpMethodAllowed('delete'));
        $this->assertFalse($spec->isHttpMethodAllowed('anything'));
    }


    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function creatingRouteSpecWithUriScheme()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);
        $this->assertNull($spec->getUriScheme());
      
        $data = $this->getDefaultData();
        $data['uri-scheme'] = 'http';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals('http', $spec->getUriScheme());

        /* gets converted to uppercase */ 
        $data = $this->getDefaultData();
        $data['uri-scheme'] = 'HTTPS';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals('https', $spec->getUriScheme());

        /* only checks its a valid string */
        $data = $this->getDefaultData();
        $data['uri-scheme'] = 'ftp';
        $spec = $this->createRouteSpec($data);
        $this->assertEquals('ftp', $spec->getUriScheme());

        return $spec;
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function isUrlSchemeAllowedNoSchemeDeclared()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);
        $this->assertNull($spec->getUriScheme());
        
        $this->assertTrue($spec->isUriSchemeAllowed()); 
        $this->assertTrue($spec->isUriSchemeAllowed('http'));
        $this->assertTrue($spec->isUriSchemeAllowed('https'));
        $this->assertTrue($spec->isUriSchemeAllowed('ftp'));
        $this->assertTrue($spec->isUriSchemeAllowed('git'));
    }

    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function isUrlSchemeAllowedSchemeDeclared()
    {
        $data = $this->getDefaultData();
        $data['uri-scheme'] = 'https';
        $spec = $this->createRouteSpec($data);
        
        $this->assertFalse($spec->isUriSchemeAllowed()); 
        $this->assertFalse($spec->isUriSchemeAllowed('http'));
        $this->assertTrue($spec->isUriSchemeAllowed('https'));
        $this->assertFalse($spec->isUriSchemeAllowed('ftp'));
        $this->assertFalse($spec->isUriSchemeAllowed('git'));
    }


    /**
     * @test
     * @depends creatingRouteSpec
     * @return  RouteSpec
     */
    public function isUrlSchemeAllowedBadArgFailure()
    {
        $data = $this->getDefaultData();
        $spec = $this->createRouteSpec($data);
       
        $msg = 'uri scheme must be null or a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg); 
        $spec->isUriSchemeAllowed(new StdClass);
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
    public function creatingSpecInvalidPattern($badPattern) 
    { 
        $msg = 'pattern must be a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['pattern'] = $badPattern; 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @depends         creatingRouteSpec
     * @dataProvider    provideInvalidStringsIncludeEmpty
     * @return          null 
     */
    public function creatingSpecInvalidController($badClassName) 
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
    public function creatingSpecInvalidRouteParams($badParam) 
    { 
        $msg = 'route parameter must be a non empty string'; 
        $this->setExpectedException('OutOfBoundsException', $msg); 

        $data = $this->getDefaultData();
        $data['params'] = array('name', $badParam, 'type'); 
        $spec = $this->createRouteSpec($data); 
    }

    /** 
     * @test
     * @depends         creatingRouteSpec
     * @return          null 
     */
    public function creatingSpecInvalidHttpMethod() 
    { 
        $msg = 'http method must be null, array or a non empty string'; 
        $this->setExpectedException('InvalidArgumentException', $msg); 

        $data = $this->getDefaultData();
        $data['http-method'] = new StdClass(); 
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
