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
     * @return  string
     */
    public function getPathCollectionInterface()
    {
        return 'Appfuel\\Filesystem\\PathCollectionInterface';
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
    public function createAppKernel($root, $env, $debug = null, $list = null)
    {
        return new AppKernel($root, $env, $debug, $list);
    }
    
    /**
     * @test
     * @return  AppKernel
     */
    public function creatingAppKernelWithDebugging()
    {
        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = true;
        $app = $this->createAppKernel($root, $env, $isDebug);
        $interface = 'Appfuel\\Kernel\\AppKernelInterface';
        $this->assertInstanceOf($interface, $app);
        $this->assertEquals($env, $app->getEnv());
        $this->assertTrue($app->isDebuggingEnabled());

        return $app;
    }

    /**
     * @test
     * @depends  creatingAppKernelWithDebugging
     */
    public function creatingAppKernelWithDebuggingErrorDisplayReportLevel()
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));


        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = true;
        $app = $this->createAppKernel($root, $env, $isDebug);

        $this->assertEquals('1', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));
    }

    /**
     * @test
     * @depends  creatingAppKernelWithDebugging
     */
    public function creatingAppKernelWithDebuggingErrorHandling()
    {
        $errorHandler = function ($nbr, $str, $file, $line, $context) {
            return true;
        };

        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = true;
        $app = $this->createAppKernel($root, $env, $isDebug);
        
        $result = set_error_handler($errorHandler); 
        $this->assertInternalType('array', $result);
        $this->assertEquals(2, count($result));

        $obj = current($result);
        $this->assertInstanceOf('Appfuel\\Kernel\\FaultHandler', $obj);

        $method = next($result);
        $this->assertEquals('handleError', $method);

        return $app;
    } 
 
    /**
     * @test
     * @depends  creatingAppKernelWithDebugging
     */
    public function creatingAppKernelWithDebuggingExceptionHandling()
    {
        $exceptionHandler = function ($e) {
            return true;
        };

        $root = '/my/root/path';
        $env  = 'dev';
        $isDebug = true;
        $app = $this->createAppKernel($root, $env, $isDebug);
        
        $result = set_exception_handler($exceptionHandler); 
        $this->assertInternalType('array', $result);
        $this->assertEquals(2, count($result));

        $obj = current($result);
        $this->assertInstanceOf('Appfuel\\Kernel\\FaultHandler', $obj);

        $method = next($result);
        $this->assertEquals('handleException', $method);
    } 
 
    /**
     * @test
     * @depends creatingAppKernelWithDebugging
     * @return  AppKernel
     */
    public function creatingAppKernelStandardPaths(AppKernel $app)
    {
        $paths = $app->getPathCollection();

        $root = '/my/root/path';
        $interface = $this->getPathCollectionInterface();
        $this->assertInstanceOf($interface, $paths);
        
        $this->assertEquals($root, $paths->getRoot());
        $list = $app->getDefaultPaths();
        $this->assertEquals($list, $paths->getMap());
        
        return $app;
    }

    /**
     * @test
     * @depends creatingAppKernelWithDebugging
     * @return  AppKernel
     */
    public function creatingAppKernelFileHandler(AppKernel $app)
    {
        $paths = $app->getPathCollection();
        $handler = $app->getFileHandler();
        $interface = 'Appfuel\\Filesystem\\FileHandlerInterface';
        $this->assertInstanceOf($interface, $handler);
        
        $this->assertEquals($handler->getRootPath(), $paths->getRoot());
        
        return $app;
    }

    /**
     * @test
     * @return  AppKernel
     */
    public function creatingAppKernelNoDebugging()
    {
        $this->assertEquals('1', ini_set('display_errors', '1'));
        $this->assertEquals('1', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));

        $root = '/my/root/path';
        $env  = 'prod';
        $app = $this->createAppKernel($root, $env);
        $interface = 'Appfuel\\Kernel\\AppKernelInterface';
        $this->assertInstanceOf($interface, $app);
        $this->assertEquals($env, $app->getEnv());
        $this->assertFalse($app->isDebuggingEnabled());

        $this->assertEquals('0', ini_get('display_errors'));
        $this->assertEquals(-1, error_reporting(0));
        return $app;
    }

    /**
     * @test
     * @depends         creatingAppKernelNoDebugging
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingAppKernelEnvFailure($badEnv)
    {
        $msg = 'environment name must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $root = '/my/root/path';
        $app = $this->createAppKernel($root, $badEnv);
    }
 
    /**
     * @test
     * @depends         creatingAppKernelNoDebugging
     * @dataProvider    provideInvalidStringsIncludeEmpty
     */
    public function creatingAppKernelRootFailure($badRoot)
    {
        $msg = 'root path must be a non empty string';
        $this->setExpectedException('InvalidArgumentException', $msg);

        $env = 'dev';
        $app = $this->createAppKernel($badRoot, $env);
    }
       
    /**
     * @test
     * @depends creatingAppKernelWithDebugging
     * @return  AppKernel
     */
    public function showingErrors(AppKernel $app)
    {
        $this->assertEquals('1', ini_set('display_errors', '0'));
        $this->assertEquals('0', ini_get('display_errors'));

        $this->assertSame($app, $app->showErrors());
        $this->assertEquals('1', ini_get('display_errors'));

        return $app;
    }

    /**
     * @test 
     * @depends showingErrors
     * @return  AppKernel
     */
    public function hidingErrors(AppKernel $app)
    {
        $this->assertEquals('1', ini_get('display_errors'));

        $this->assertSame($app, $app->hideErrors());
        $this->assertEquals('0', ini_get('display_errors'));

        return $app;
    }

    /**
     * @test
     * @depends hidingErrors
     * @return  AppKernel
     */
    public function disableErrorReporting(AppKernel $app)
    {
        $this->assertEquals(-1, error_reporting());
        $this->assertSame($app, $app->disableErrorReporting());
        $this->assertEquals(0, error_reporting());

        return $app;
    }

    /**
     * @test
     * @depends disableErrorReporting
     * @return  AppKernel
     */
    public function enableFullErrorReporting(AppKernel $app)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $this->assertSame($app, $app->enableFullErrorReporting());
        $this->assertEquals(-1, error_reporting());

        return $app;
    }

    /**
     * This is a simple wrapper so there is no need for extensive testing
     *
     * @test
     * @depends enableFullErrorReporting
     * @return  AppKernel
     */
    public function setErrorReporting(AppKernel $app)
    {
        $this->assertEquals(-1, error_reporting(0));
        $this->assertEquals(0, error_reporting());
        
        $level = E_ERROR;
        $this->assertSame($app, $app->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());

        $level = E_PARSE;
        $this->assertSame($app, $app->setErrorReporting($level));
        $this->assertEquals($level, error_reporting());
    
        return $app;
    }

    /**
     * @test
     * @dataProvider    provideInvalidInts
     * @depends         setErrorReporting
     */
    public function setErrorReportingFailure($badLevel)
    {
        $msg = 'error level must be an int';
        $this->setExpectedException('InvalidArgumentException', $msg);
        
        $root = '/my/root';
        $env = 'prod';
        $app = $this->createAppKernel($root, $env);
        $app->setErrorReporting($badLevel);
    }

    /**
     * @test 
     * @depends creatingAppKernelWithDebugging
     * @return  AppKernel
     */
    public function applicationDebugging(AppKernel $app)
    {
        $this->assertTrue($app->isDebuggingEnabled());
        
        $this->assertSame($app, $app->disableDebugging());
        $this->assertFalse($app->isDebuggingEnabled());
        
        $this->assertSame($app, $app->enableDebugging());
        $this->assertTrue($app->isDebuggingEnabled());
        
        return $app;
    }

    /**
     * @test
     * @depends applicationDebugging
     * @return  AppKernel
     */
    public function creatingPathCollection(AppKernel $app)
    {
        $root = '/my/root';
        $paths = $app->createPathCollection($root);
        $interface = $this->getPathCollectionInterface();
        $this->assertInstanceOf($interface, $paths);
        $this->assertEquals($root, $paths->getRoot());

        $list = array('my-path' => 'some/path');
        $paths = $app->createPathCollection($root, $list);
        $this->assertInstanceOf($interface, $paths);
        $this->assertTrue($paths->exists('my-path'));
        
        return $app;
    }

    /**
     * @test
     * @depends creatingPathCollection
     * @return  AppKernel
     */
    public function pathCollection(AppKernel $app)
    {
        $backup = $app->getPathCollection();

        $interface = $this->getPathCollectionInterface();
        $paths = $this->getMock($interface);
        $this->assertSame($app, $app->setPathCollection($paths));
        $this->assertSame($paths, $app->getPathCollection());
 
        $app->setPathCollection($backup);
        return $app;
    }

    /**
     * @test
     * @depends pathCollection
     * @return  AppKernel
     */
    public function fileHandler(AppKernel $app)
    {
        $backup = $app->getFileHandler();

        $handler = $this->getMock($this->getFileHandlerInterface());
        $this->assertSame($app, $app->setFileHandler($handler));
        $this->assertSame($handler, $app->getFileHandler());
        
        $app->setFileHandler($backup);
        return $app;
    }

    /**
     * @test
     * @depends fileHandler
     * @return  AppKernel
     */
    public function createFileHandler(AppKernel $app)
    {
        $handler = $app->createFileHandler();
        $this->assertInstanceOf('Appfuel\\Filesystem\\FileHandler', $handler);
        $this->assertNull($handler->getRootPath());

        $root = '/my/root';
        $handler = $app->createFileHandler($root);
        $this->assertInstanceOf('Appfuel\\Filesystem\\FileHandler', $handler);
        $this->assertEquals($root, $handler->getRootPath());
    }
}
