<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Testfuel\Kernel;

use StdClass,
    Testfuel\FrameworkTestCase,
    Appfuel\Kernel\ApplicationBuilder;

class ApplicationBuilderTest extends FrameworkTestCase 
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
     * @return  ApplicationBuilder
     */
    public function createApplicationBuilder($root, $env)
    {
        return new ApplicationBuilder($root, $env);
    }
    
    /**
     * @test
     * @return  ApplicationBuilder
     */
    public function creatingApplicationBuilder()
    {
        $root = __DIR__;
        $env  = 'dev';
        $builder = $this->createApplicationBuilder($root, $env);

        $interface = 'Appfuel\\Kernel\\ApplicationBuilderInterface';
        $this->assertInstanceOf($interface, $builder);
        $this->assertEquals($root, $builder->getRootPath());
        $this->assertEquals($env, $builder->getEnv());
        $this->assertFalse($builder->isDebuggingEnabled());

        return $builder;
    }

    /**
     * @test
     * @depends         creatingApplicationBuilder
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingApplicationBuilderRootPathFailure($badPath)
    {
        $msg = 'the application root path must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $builder = $this->createApplicationBuilder($badPath, 'dev');
    }

    /**
     * @test
     * @depends         creatingApplicationBuilder
     */
    public function creatingApplicationBuilderRootPathNotAbsoluteFailure($badPath)
    {
        $msg = 'the application root path must be an absolute path';
        $this->setExpectedException('DomainException', $msg);

        $builder = $this->createApplicationBuilder('some/path', 'dev');
    }
       
    /**
     * @test
     * @depends         creatingApplicationBuilder
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingApplicationBuilderEnvFailure($badEnv)
    {
        $msg = 'environment name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $builder = $this->createApplicationBuilder(__DIR__, $badEnv);
    }
    
    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function showingErrors(ApplicationBuilder $builder)
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));

        $this->assertSame($builder, $builder->showErrors());
        $this->assertEquals('1', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function hidingErrors(ApplicationBuilder $builder)
    {
        $this->assertEquals('1', ini_get('display_errors'));

        $this->assertSame($builder, $builder->hideErrors());
        $this->assertEquals('0', ini_get('display_errors'));
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function disableErrorReporting(ApplicationBuilder $builder)
    {
        $this->assertEquals(-1, error_reporting());
        $this->assertSame($builder, $builder->disableErrorReporting());
        $this->assertEquals(0, error_reporting());
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function enableFullErrorReporting(ApplicationBuilder $builder)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($builder, $builder->enableFullErrorReporting());
        $this->assertEquals(-1, error_reporting());
    }

    /**
     * This is a simple wrapper so there is no need for extensive testing
     *
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function setErrorReporting(ApplicationBuilder $builder)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $level = E_ERROR;
        $this->assertSame($builder, $builder->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());

        $level = E_PARSE;
        $this->assertSame($builder, $builder->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());
    }

    /**
     * @test
     * @dataProvider    provideInvalidInts
     * @depends         creatingApplicationBuilder
     */
    public function setErrorReportingFailure($badLevel)
    {
        $msg = 'error level must be an int';
        $this->setExpectedException('InvalidArgumentException', $msg);
        $builder = $this->createApplicationBuilder(__DIR__, 'production');
        $builder->setErrorReporting($badLevel);
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function applicationDebugging(ApplicationBuilder $builder)
    {
        $this->assertFalse($builder->isDebuggingEnabled());
        
        $this->assertSame($builder, $builder->enableDebugging());
        $this->assertTrue($builder->isDebuggingEnabled());
        
        $this->assertSame($builder, $builder->disableDebugging());
        $this->assertFalse($builder->isDebuggingEnabled());

        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function fileHandler(ApplicationBuilder $builder)
    {
        $handler = $this->getMock('Appfuel\\Filesystem\\FileHandlerInterface');
        $this->assertNull($builder->getFileHandler());
        $this->assertSame($builder, $builder->setFileHandler($handler));
        $this->assertSame($handler, $builder->getFileHandler());
        
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function eventDispatcher(ApplicationBuilder $builder)
    {
        $dispatch = $this->getMock('Appfuel\\Kernel\\EventDispatcherInterface');
        $this->assertNull($builder->getEventDispatcher());
        $this->assertSame($builder, $builder->setEventDispatcher($dispatch));
        $this->assertSame($dispatch, $builder->getEventDispatcher());
        
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function DiManager(ApplicationBuilder $builder)
    {   
        $iface = 'Appfuel\\Kernel\\DependencyInjection\\DiManagerInterface';
        $diManager = $this->getMock($iface);
        $this->assertNull($builder->getDependencyInjectionManager());
        $this->assertSame(
            $builder,
            $builder->setDependencyInjectionManager($diManager)
        );
        $this->assertSame(
            $diManager, 
            $builder->getDependencyInjectionManager()
        );
        
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function appSettings(ApplicationBuilder $builder)
    {
        $data = $this->getMock('Appfuel\\DataStructure\\ArrayDataInterface');
        $this->assertNull($builder->getSettings());
        $this->assertSame($builder, $builder->setSettings($data));
        $this->assertSame($data, $builder->getSettings());
        
        return $builder;
    }


}
