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
     * @param   PathCollectionInterface $paths
     * @param   bool    $isDebug
     * @return  ApplicationBuilder
     */
    public function createApplication($env, $paths, $debug = null)
    {
        return new Application($env, $paths, $debug);
    }

    /**
     * @test
     * @return  Application
     */
    public function creatingApplicationDefaultDebug()
    {
        $env = 'dev';
        $paths = $this->getMock($this->getPathCollectionInterface());

        $app = $this->createApplication($env, $paths);

        $interface = 'Appfuel\\Kernel\\ApplicationInterface';
        $this->assertInstanceOf($interface, $app);

        $this->assertEquals($env, $app->getEnv());
        $this->assertFalse($app->isDebuggingEnabled());
        $this->assertSame($paths, $app->getPathCollection());

        return $app;
    }

    /**
     * @test
     * @return  Application
     */
    public function creatingApplicationWithDebug()
    {
        $env = 'dev';
        $paths = $this->getMock($this->getPathCollectionInterface());

        $app = $this->createApplication($env, $paths, true);
        $interface = 'Appfuel\\Kernel\\ApplicationInterface';
        $this->assertInstanceOf($interface, $app);
        $this->assertEquals($env, $app->getEnv());
        $this->assertSame($paths, $app->getPathCollection());
        $this->assertTrue($app->isDebuggingEnabled());
        
        return $app;
    }

    /**
     * @test
     * @depends creatingApplicationDefaultDebug
     * @return  Application
     */
    public function fileHandler()
    {
        $root = '/my/path';
        $paths = $this->getMock($this->getPathCollectionInterface());
        $paths->expects($this->once())
             ->method('getRootPath')
             ->will($this->returnValue($root));

        $app = $this->createApplication('dev', $paths);
        
        $handler = $this->getMock($this->getFileHandlerInterface());
        $handler->expects($this->once())
                ->method('getRootPath')
                ->will($this->returnValue($root));

               
        $this->assertNull($app->getFileHandler());
        $this->assertSame($app, $app->setFileHandler($handler));
        $this->assertSame($handler, $app->getFileHandler());
    }

    /**
     * @test
     * @depends creatingApplicationDefaultDebug
     */
    public function setFileHandlerPathCollectionDifferentRootsFailure()
    {
        $paths = $this->getMock($this->getPathCollectionInterface());
        $paths->expects($this->once())
              ->method('getRootPath')
              ->will($this->returnValue('/my/path'));
        
        $app = $this->createApplication('dev', $paths);

        $handler = $this->getMock($this->getFileHandlerInterface());
        $handler->expects($this->once())
                ->method('getRootPath')
                ->will($this->returnValue('/your/path'));
 

        $msg  = 'The root path for both path collection and file handler '; 
        $msg .= 'must be the same';
        $this->setExpectedException('LogicException', $msg);

        $app->setFileHandler($handler);
    }

    /**
     * @test
     * @depends creatingApplicationDefaultDebug
     */
    public function eventDispatcher(Application $app)
    {
        $dispatch = $this->getMock('Appfuel\\Kernel\\EventDispatcherInterface');
        $this->assertNull($app->getEventDispatcher());
        $this->assertSame($app, $app->setEventDispatcher($dispatch));
        $this->assertSame($dispatch, $app->getEventDispatcher());
    }

    /**
     * @test
     * @depends creatingApplicationDefaultDebug
     */
    public function DiManager(Application $app)
    {   
        $iface = 'Appfuel\\Kernel\\DependencyInjection\\DiManagerInterface';
        $diManager = $this->getMock($iface);
        $this->assertNull($app->getDependencyInjectionManager());
        $this->assertSame($app,$app->setDependencyInjectionManager($diManager));
        $this->assertSame($diManager, $app->getDependencyInjectionManager());
    }

    /**
     * @test
     * @depends creatingApplicationDefaultDebug
     */
    public function appConfigSettings(Application $app)
    {
        $data = $this->getMock('Appfuel\\DataStructure\\ArrayDataInterface');
        $this->assertNull($app->getConfigSettings());
        $this->assertSame($app, $app->setConfigSettings($data));
        $this->assertSame($data, $app->getConfigSettings());
    }
}
