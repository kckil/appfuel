<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Kernel\AppInitializer;

class AppInitializerTest extends FrameworkTestCase 
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
     * @return  AppInitializer
     */
    public function createInitializer()
    {
        return new AppInitializer();
    }
    
    /**
     * @test
     * @return  AppInitializer
     */
    public function creatingAnAppInitializer()
    {
        $init = $this->createInitializer();

        $interface = 'Appfuel\\Kernel\\AppInitializerInterface';
        $this->assertInstanceOf($interface, $init);

        return $init;
    }
    
    /**
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function showingErrors(AppInitializer $init)
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));

        $this->assertSame($init, $init->showErrors());
        $this->assertEquals('1', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function hidingErrors(AppInitializer $init)
    {
        $this->assertEquals('1', ini_get('display_errors'));

        $this->assertSame($init, $init->hideErrors());
        $this->assertEquals('0', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function disableErrorReporting(AppInitializer $init)
    {
        $this->assertEquals(-1, error_reporting());
        $this->assertSame($init, $init->disableErrorReporting());
        $this->assertEquals(0, error_reporting());
    }

    /**
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function enableFullErrorReporting(AppInitializer $init)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($init, $init->enableFullErrorReporting());
        $this->assertEquals(-1, error_reporting());
    }

    /**
     * This is a simple wrapper so there is no need for extensive testing
     *
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function setErrorReporting(AppInitializer $init)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $level = E_ERROR;
        $this->assertSame($init, $init->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());

        $level = E_PARSE;
        $this->assertSame($init, $init->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());
    }

    /**
     * @test
     * @dataProvider    provideInvalidInts
     * @depends         creatingAnAppInitializer
     */
    public function setErrorReportingFailure($badLevel)
    {
        $msg = 'error level must be an int';
        $this->setExpectedException('InvalidArgumentException', $msg);
        $init = $this->createInitializer('production');
        $init->setErrorReporting($badLevel);
    }

    /**
     * @test
     * @depends creatingAnAppInitializer
     * @return  AppInitializer
     */
    public function enableDebugging(AppInitializer $init)
    {
        $this->assertEquals('1', ini_set('display_errors', '0')); 
        $this->assertEquals('0', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($init, $init->enableDebugging());
        $this->assertEquals(-1, error_reporting());
        $this->assertEquals('1', ini_get('display_errors'));
    }
}
