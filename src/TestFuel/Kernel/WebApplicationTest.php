<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Kernel\WebApplication;

class WebApplicationTest extends FrameworkTestCase 
{

    /**
     * @return  null
     */
    public function setUp()
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);

        ini_set('display_errors', '1');
        error_reporting(-1);
        restore_error_handler();
    }

    /**
     * @return  null
     */
    public function tearDown()
    {
        ini_set('display_errors', '1');
        error_reporting(-1);
        restore_error_handler();
    }

    /**
     * @param   string  $evn    name of env app is running in
     * @return  ApplicationBuilder
     */
    public function createWebApp($root, $env, $debug = null, $list = null)
    {
        return new WebApplication($root, $env, $debug, $list);
    }
    
    /**
     * @test
     * @return  WebApplication
     */
    public function creatingWebApplicationWithDebugging()
    {
        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = true;
        $app = $this->createWebApp($root, $env, $isDebug);
        $interface = 'Appfuel\\Kernel\\AppKernelInterface';
        $this->assertInstanceOf($interface, $app);

        $interface = 'Appfuel\\Kernel\\WebInterface';
        $this->assertInstanceOf($interface, $app);
        
        $this->assertEquals($env, $app->getEnv());
        $this->assertTrue($app->isDebuggingEnabled());

        return $app;
    }

    /**
     * @test
     * @depends creatingWebApplicationWithDebugging
     * @return  WebApplication
     */
    public function creatingWebApplicationNoDebugging()
    {
        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = false;
        $app = $this->createWebApp($root, $env, $isDebug);
        $this->assertFalse($app->isDebuggingEnabled());

        return $app;
    }

    /**
     * @test
     * @depends creatingWebApplicationWithDebugging
     * @return  WebApplication
     */
    public function gettingTheRequestMethodNormal(WebApplication $app)
    {
        $method = 'post';
        $_SERVER['REQUEST_METHOD'] = $method;
        $this->assertEquals($method, $app->getRequestMethod());

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->assertEquals('post', $app->getRequestMethod());
        
        $_SERVER['REQUEST_METHOD'] = 'PosT';
        $this->assertEquals('post', $app->getRequestMethod());

        return $app;
    }

    /**
     * @test
     * @depends gettingTheRequestMethodNormal
     * @return  WebApplication
     */
    public function gettingTheRequestMethodXOverride(WebApplication $app)
    {
        $method = 'get';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = $method;
        $this->assertEquals($method, $app->getRequestMethod());

        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'GET';
        $this->assertEquals('get', $app->getRequestMethod());
        
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'GeT';
        $this->assertEquals('get', $app->getRequestMethod());

        return $app;
    }

    /**
     * @test
     * @depends gettingTheRequestMethodXOverride
     * @return  WebApplication
     */
    public function gettingTheRequestMethodBoth(WebApplication $app)
    {
        $_SERVER['REQUEST_METHOD'] = 'post';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'get';
        
        $this->assertEquals('get', $app->getRequestMethod());

        return $app;
    }

    /**
     * @test
     * @depends gettingTheRequestMethodBoth
     * @return  null
     */
    public function gettingTheRequestMethodNone(WebApplication $app)
    {
        $msg = 'http request method was not set';
        $this->setExpectedException('LogicException', $msg);

        $result = $app->getRequestMethod();
    }



}
