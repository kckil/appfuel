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
     * @return  string
     */
    public function getPathCollectionInterface()
    {
        return 'Appfuel\\Kernel\\PathCollectionInterface';
    }

    /**
     * @return  string
     */
    public function getFileHandlerInterface()
    {
        return 'Appfuel\\Filesystem\\FileHandlerInterface';
    }

    /**
     * @param   string  $evn    name of env app is running in
     * @return  ApplicationBuilder
     */
    public function createApplicationBuilder($env)
    {
        return new ApplicationBuilder($env);
    }
    
    /**
     * @test
     * @return  ApplicationBuilder
     */
    public function creatingApplicationBuilder()
    {
        $env  = 'dev';
        $builder = $this->createApplicationBuilder($env);

        $interface = 'Appfuel\\Kernel\\ApplicationBuilderInterface';
        $this->assertInstanceOf($interface, $builder);
        $this->assertEquals($env, $builder->getEnv());

        return $builder;
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

        $builder = $this->createApplicationBuilder($badEnv);
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
    public function creatingPathCollectionJustRoot(ApplicationBuilder $builder)
    {
        $root = '/my/root';
        $paths = $builder->createPathCollection($root);
        $interface = $this->getPathCollectionInterface();
        $this->assertInstanceOf($interface, $paths);
        $this->assertEquals($root, $paths->getRootPath());

        $list = array('my-path' => 'some/path');
        $paths = $builder->createPathCollection($root, $list);
        $this->assertInstanceOf($interface, $paths);
        $this->assertTrue($paths->isPath('my-path'));
        
        return $builder;
    }

    /**
     * @test    
     * @depends creatingPathCollectionJustRoot
     * @return  ApplicationBuilder
     */
    public function pathCollection(ApplicationBuilder $builder)
    {
        $this->assertNull($builder->getPathCollection());
        $this->assertFalse($builder->isPathCollection());

        $interface = $this->getPathCollectionInterface();
        $paths = $this->getMock($interface);
        $this->assertSame($builder, $builder->setPathCollection($paths));
        $this->assertTrue($builder->isPathCollection());
        $this->assertSame($paths, $builder->getPathCollection());
 
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     */
    public function loadStandardAppfuelPaths()
    {
        $builder = $this->createApplicationBuilder('dev');
        $this->assertNull($builder->getPathCollection());

        $root = '/my/path';
        $this->assertSame($builder, $builder->loadStandardPaths($root));
        $paths = $builder->getPathCollection();
        
        $interface = $this->getPathCollectionInterface();
        $this->assertInstanceOf($interface, $paths);

        $afpath = 'vendor/appfuel/appfuel';
        $this->assertTrue($paths->isPath('appfuel'));
        $this->assertEquals($afpath, $paths->getRelativePath('appfuel'));
        $this->assertEquals("$root/$afpath", $paths->getPath('appfuel'));

        $expected = "$afpath/src";
        $this->assertTrue($paths->isPath('appfuel-src'));
        $this->assertEquals($expected, $paths->getRelativePath('appfuel-src'));
        $this->assertEquals("$root/$expected", $paths->getPath('appfuel-src'));

        $expected = "$afpath/bin";
        $this->assertTrue($paths->isPath('appfuel-bin'));
        $this->assertEquals($expected, $paths->getRelativePath('appfuel-bin'));
        $this->assertEquals("$root/$expected", $paths->getPath('appfuel-bin'));

 
        return $builder; 
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function fileHandler(ApplicationBuilder $builder)
    {
        $handler = $this->getMock($this->getFileHandlerInterface());
        $this->assertNull($builder->getFileHandler());
        $this->assertFalse($builder->isFileHandler());
        $this->assertSame($builder, $builder->setFileHandler($handler));
        $this->assertTrue($builder->isFileHandler());
        $this->assertSame($handler, $builder->getFileHandler());
        
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function createFileHandler(ApplicationBuilder $builder)
    {
        $handler = $builder->createFileHandler();
        $this->assertInstanceOf('Appfuel\\Filesystem\\FileHandler', $handler);
        $this->assertNull($handler->getRootPath());

        $root = '/my/root';
        $handler = $builder->createFileHandler($root);
        $this->assertInstanceOf('Appfuel\\Filesystem\\FileHandler', $handler);
        $this->assertEquals($root, $handler->getRootPath());
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     */
    public function loadFileHandler()
    {
        $builder = $this->createApplicationBuilder('dev');
        $paths = $this->getMock($this->getPathCollectionInterface());
        $paths->expects($this->any())
              ->method('getRootPath')
              ->will($this->returnValue('/my/path'));

        $builder->setPathCollection($paths);
        
        $this->assertSame($builder, $builder->loadFileHandler());
        $this->assertTrue($builder->isFileHandler());
        
        $handler = $builder->getFileHandler();
        $this->assertEquals($paths->getRootPath(), $handler->getRootPath());
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     */
    public function loadFileHandlerPathNotSetFailure()
    {
        $builder = $this->createApplicationBuilder('dev');
        
        $msg  = 'The path collection must be set before the file handler ';
        $msg .= 'is loaded';
        $this->setExpectedException('LogicException', $msg);

        $builder->loadFileHandler();
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     */
    public function setFileHandlerWhenNotPathCollectionFailure()
    {
        $builder = $this->createApplicationBuilder('dev');
        $handler = $this->getMock($this->getFileHandlerInterface());
        $this->assertFalse($builder->isPathCollection());

        $msg = 'The path collection must be set before the file handler';
        $this->setExpectedException('LogicException', $msg);

        $builder->setFileHandler($handler);
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     */
    public function setFileHandlerPathCollectionDifferentRootsFailure()
    {
        $builder = $this->createApplicationBuilder('dev');
        $paths = $this->getMock($this->getPathCollectionInterface());
        $paths->expects($this->once())
              ->method('getRootPath')
              ->will($this->returnValue('/my/path'));
        
        $builder->setPathCollection($paths);

        $handler = $this->getMock($this->getFileHandlerInterface());
        $handler->expects($this->once())
                ->method('getRootPath')
                ->will($this->returnValue('/your/path'));
 
        $this->assertTrue($builder->isPathCollection());

        $msg  = 'The root path of the file handler and path collection must ';
        $msg .= 'be the same';
        $this->setExpectedException('LogicException', $msg);

        $builder->setFileHandler($handler);
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
        $this->assertFalse($builder->isSettings());
        $this->assertSame($builder, $builder->setSettings($data));
        $this->assertTrue($builder->isSettings());
        $this->assertSame($data, $builder->getSettings());
        
        return $builder;
    }

    /**
     * @test
     * @depends creatingApplicationBuilder
     * @return  ApplicationBuilder
     */
    public function loadAppSettings()
    {
        $root = "{$this->getFixturePath()}/app-root";
        $builder = $this->createApplicationBuilder('dev');
        $builder->loadStandardPaths($root)
                ->loadFileHandler();
        
       
        $builder->loadSettings();
        $this->assertTrue($builder->isSettings());

        $settings = $builder->getSettings();
        $data = $settings->get('section-a');
        $expected = array('a', 'b', 'c');
        $this->assertEquals($expected, $data);
        
        return $builder;
    }


}
