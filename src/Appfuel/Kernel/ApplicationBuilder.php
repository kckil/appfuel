<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use LogicException,
    DomainException,
    InvalidArgumentException,
    Appfuel\Console\ConsoleInputInterface,
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\PathFinder,
    Appfuel\Filesystem\PathCollection,
    Appfuel\Filesystem\PathCollectionInterface,
    Appfuel\Filesystem\FileHandlerInterface,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Kernel\AppPathInterface,
    Appfuel\Kernel\Route\RouteManagerInterface,
    Appfuel\Kernel\DependencyInjection\DiManager,
    Appfuel\Kernel\DependencyInjection\DiManagerInterface;

class ApplicationBuilder implements ApplicationBuilderInterface
{
    /**
     * Name of the environment this kernel is running in
     * @var string
     */
    protected $env = null;
    
    /**
     * @var bool
     */
    protected $isDebug = false;
    
    /**
     * Used for all file operations with the application root
     * @var FileHandlerInterface
     */
    protected $fileHandler = null;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher = null;    

    /**
     * @var DiManagerInterface
     */   
    protected $diManager = null;

    /**
     * @var RouteManagerInterface
     */
    protected $routeManager = null;

    /**
     * @param   string  $root
     * @param   string  $env
     * @param   FileHandlerInterface $fileHandler
     * @return  Application
     */
    public function __construct($env, $root, array $paths = array())
    {
        if (! is_string($env) || empty($env)) {
            $err = "environment name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->env = $env;

        $this->loadFileHandler($root, $paths);
    }

    /**
     * @return  string
     */
    public function getEnv()
    {
        return $this->env;
    }
    
    public function getDefaultPaths()
    {
        $env = $this->getEnv();
        $vendor = 'vendor/appfuel/appfuel';
        return array(
            'www'                => 'www',
            'bin'                => 'bin',
            'src'                => 'src',
            'app'                => 'app',
            'vendor'             => 'vendor',
            'cache-dir'          => 'app/cache',
            'config-dir'         => 'app/config',
            'framework-cache'    => 'app/cache/framework.cache.php',
            'container-cache'    => "app/cache/{$env}/container.cache.php",
            'app-settings'       => "app/config/settings-{$env}.yml",
            'app-settings-cache' => "app/cache/{$env}/app-settings.cache.php",
            'appfuel-src'        => "$vendor/src",
            'appfuel-bin'        => "$vendor/bin",
            
        );
    }

    /**
     * @param   string  $root
     * @param   array   $paths
     * @return  ApplicationBuilder
     */
    public function loadFileHandler($root, array $list = null)
    {
        $paths = $this->getDefaultPaths();
        if (null != $list) {
            $paths = array_merge($paths, $list);
        }
        $collection = $this->createPathCollection($root, $paths);
        $finder = $this->createPathFinder($collection);
        $this->setFileHandler($this->createFileHandler($finder));
        
        return $this;
    }

    /**
     * @param   string  $root
     * @return  array   $paths
     */
    public function createPathCollection($root, array $paths = array())
    {
        return new PathCollection($root, $paths);
    }

    /**
     * @param   PathCollectionInterface $paths
     * @return  PathFinder
     */
    public function createPathFinder(PathCollectionInterface $paths)
    {
        return new PathFinder($paths);
    }

    /**
     * @return  AppInitializer
     */
    public function showErrors()
    {
        ini_set('display_errors', '1');
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function hideErrors()
    {
        ini_set('display_errors', '0');
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function disableErrorReporting()
    {
        error_reporting(0);
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function enableFullErrorReporting()
    {
        error_reporting(-1);
        return $this;
    }

    /**
     * @param   int $level
     * @return  AppInitializer
     */
    public function setErrorReporting($level)
    {
        if (! is_int($level)) {
            throw new InvalidArgumentException("error level must be an int");
        }

        error_reporting($level);
        return $this;
    }

    /**
     * @return  Application
     */
    public function enableDebugging()
    {
        $this->isDebug = true;
        return $this;
    }

    /**
     * @return  Application
     */
    public function disableDebugging()
    {
        $this->isDebug = false;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isDebuggingEnabled()
    {
        return  $this->isDebug;
    }

    /**
     * @param   callable    $handler
     * @return  AppInitializer
     */
    public function registerExceptionHandler($handler)
    {
        set_exception_handler($handler);
        return $this;
    }

    /**
     * @param   callable    $handler
     * @param   int         $errorTypes
     * @return  AppInitializer
     */
    public function registerErrorHandler($handler, $types = null)
    {
        set_error_handler($handler, $types);
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function registerAppFuelFaultHandler()
    {
        $handler = new FaultHandler();
        $this->registerExceptionHandler(array($handler, 'handleException'))
             ->registerErrorHandler(array($handler, 'handleError'));

        return $this;
    }

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * @param   FileHandlerInterface    $fileHandler
     * @return  ApplicationBuilder
     */
    public function setFileHandler(FileHandlerInterface $fileHandler)
    {
        $this->fileHandler = $fileHandler;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isFileHandler()
    {
        return $this->fileHandler instanceof FileHandlerInterface;
    }

    /**
     * @param   string  $rootPath
     * @return  FileHandler
     */
    public function createFileHandler($rootPath = null)
    {
        return new FileHandler($rootPath);
    }

    /**
     * @return  FileHandlerInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param   FileHandlerInterface    $fileHandler
     * @return  ApplicationBuilder
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->eventDispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return  DiManagerInterface
     */
    public function getDependencyInjectionManager()
    {
        return $this->diManager;
    }

    /**
     * @param   DiManagerInterface  $manager
     * @return  ApplicationBuilder
     */
    public function setDependencyInjectionManager(DiManagerInterface $manager)
    {
        $this->diManager = $manager;
        return $this;
    }

    /**
     * @return  Application
     */
    public function build($type)
    {
        if (! $this->isFileHandler()) {
            throw new DomainException("file handler is required");
        }
        
        $env = $this->getEnv();
        $debug = $this->isDebuggingEnabled();  
        $handler = $this->getFileHandler(); 
        if ('web' === $type) {
            $app = $this->createWebApp($env, $handler, $debug);
        }
        else {
            $app = $this->createConsoleApp($env, $handler, $debug);
        }

        return $app;
    }

    /**
     * @param   ConsoleInputInterface   $input
     * @return  ConsoleApplication
     */
    public function buildForConsole(ConsoleInputInterface $input)
    {
        $console = $this->build('console');
        $console->setInput($input);

        return $console; 
    }

    /**
     * @param   string  $env
     * @param   FileHandlerInterface $p
     * @param   bool    $debug
     * @return  ConsoleApplication
     */
    protected function createWebApp($env, FileHandlerInterface $f, $debug)
    {
        return new Application($env, $f, $debug);
    }

    /**
     * @param   string  $env
     * @param   FileHandlerInterface $p
     * @param   bool    $debug
     * @return  ConsoleApplication
     */
    protected function createConsoleApp($env, FileHandlerInterface $f, $debug)
    {
        return new ConsoleApplication($env, $f, $debug);
    }
}
