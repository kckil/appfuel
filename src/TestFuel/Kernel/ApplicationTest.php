<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Appfuel\Kernel\Application,
    Testfuel\FrameworkTestCase;

class ApplicationTest extends FrameworkTestCase 
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
     * @param   string  $root   app root dir
     * @param   string  $evn    name of env app is running in
     * @return  Application
     */
    public function createApplication($root, $env)
    {
        return new Application($root, $env);
    }
    
    /**
     * @test
     * @return  Application
     */
    public function creatingApplication()
    {
        $root = __DIR__;
        $env  = 'dev';
        $kernel = $this->createApplication($root, $env);

        $interface = 'Appfuel\\Kernel\\ApplicationInterface';
        $this->assertInstanceOf($interface, $kernel);

        return $kernel;
    }

    /**
     * @test
     * @depends         creatingApplication
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingApplicationEnvFailure($badEnv)
    {
        $msg = 'environment name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $kernel = $this->createApplication(__DIR__, $badEnv);
    }
    
    /**
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function showingErrors(Application $kernel)
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));

        $this->assertSame($kernel, $kernel->showErrors());
        $this->assertEquals('1', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function hidingErrors(Application $kernel)
    {
        $this->assertEquals('1', ini_get('display_errors'));

        $this->assertSame($kernel, $kernel->hideErrors());
        $this->assertEquals('0', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function disableErrorReporting(Application $kernel)
    {
        $this->assertEquals(-1, error_reporting());
        $this->assertSame($kernel, $kernel->disableErrorReporting());
        $this->assertEquals(0, error_reporting());
    }

    /**
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function enableFullErrorReporting(Application $kernel)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($kernel, $kernel->enableFullErrorReporting());
        $this->assertEquals(-1, error_reporting());
    }

    /**
     * This is a simple wrapper so there is no need for extensive testing
     *
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function setErrorReporting(Application $kernel)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $level = E_ERROR;
        $this->assertSame($kernel, $kernel->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());

        $level = E_PARSE;
        $this->assertSame($kernel, $kernel->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());
    }

    /**
     * @test
     * @dataProvider    provideInvalidInts
     * @depends         creatingApplication
     */
    public function setErrorReportingFailure($badLevel)
    {
        $msg = 'error level must be an int';
        $this->setExpectedException('InvalidArgumentException', $msg);
        $kernel = $this->createApplication(__DIR__, 'production');
        $kernel->setErrorReporting($badLevel);
    }

    /**
     * @test
     * @depends creatingApplication
     * @return  Application
     */
    public function enableDebugging(Application $kernel)
    {
        $this->assertEquals('1', ini_set('display_errors', '0')); 
        $this->assertEquals('0', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($kernel, $kernel->enableDebugging());
        $this->assertEquals(-1, error_reporting());
        $this->assertEquals('1', ini_get('display_errors'));
    }
}
