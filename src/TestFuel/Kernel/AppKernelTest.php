<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Appfuel\Kernel\AppKernel,
    Testfuel\FrameworkTestCase;

class AppKernelTest extends FrameworkTestCase 
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
     * @param   array   $spec
     * @return  AppKernel
     */
    public function createAppKernel($env)
    {
        return new AppKernel($env);
    }
    
    /**
     * @test
     * @return  AppKernel
     */
    public function creatingAppKernel()
    {
        $kernel = $this->createAppKernel('dev');

        $interface = 'Appfuel\\Kernel\\AppKernelInterface';
        $this->assertInstanceOf($interface, $kernel);

        return $kernel;
    }

    /**
     * @test
     * @depends         creatingAppKernel
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingAppKernelEnvFailure($badEnv)
    {
        $msg = 'environment name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $kernel = $this->createAppKernel($badEnv);
    }
    
    /**
     * @test
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function showingErrors(AppKernel $kernel)
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));

        $this->assertSame($kernel, $kernel->showErrors());
        $this->assertEquals('1', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function hidingErrors(AppKernel $kernel)
    {
        $this->assertEquals('1', ini_get('display_errors'));

        $this->assertSame($kernel, $kernel->hideErrors());
        $this->assertEquals('0', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function disableErrorReporting(AppKernel $kernel)
    {
        $this->assertEquals(-1, error_reporting());
        $this->assertSame($kernel, $kernel->disableErrorReporting());
        $this->assertEquals(0, error_reporting());
    }

    /**
     * @test
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function enableFullErrorReporting(AppKernel $kernel)
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
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function setErrorReporting(AppKernel $kernel)
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
     * @depends         creatingAppKernel
     */
    public function setErrorReportingFailure($badLevel)
    {
        $msg = 'error level must be an int';
        $this->setExpectedException('InvalidArgumentException', $msg);
        $kernel = $this->createAppKernel('production');
        $kernel->setErrorReporting($badLevel);
    }

    /**
     * @test
     * @depends creatingAppKernel
     * @return  AppKernel
     */
    public function enableDebugging(AppKernel $kernel)
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
