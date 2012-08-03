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
}
