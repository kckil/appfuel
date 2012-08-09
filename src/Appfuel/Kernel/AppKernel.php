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
    Appfuel\Route\RouteDispatcher,
    Appfuel\Route\RouteDispatcherInterface,
    Appfuel\Route\RouteCollectionInterface,
    Appfuel\Kernel\Cache\CodeCacheManager,
    Appfuel\Console\ConsoleInputInterface,
    Appfuel\Filesystem\PathCollection,
    Appfuel\Filesystem\PathCollectionInterface,
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\FileHandlerInterface;

class AppKernel implements AppKernelInterface
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
     * List of mapped directories and files. 
     * @var PathCollectionInterface
     */   
    protected $paths = null;
 
    /**
     * Used to interact with the filesystem
     * @var FileHandlerInterface
     */
    protected $fileHandler = null;

    /**
     * Used dipatch requests to action controller
     * @var RouteDispatcherInterface
     */
    protected $routeDispatcher = null;

    /**
     * Used to determine if the startup tasks have been run
     * @var bool
     */
    protected $isStarted = false;

    /**
     * @param   string  $root
     * @param   string  $env
     * @param   FileHandlerInterface $fileHandler
     * @return  Application
     */
    public function __construct($root, $env, $debug = null, array $paths = null)
    {
        if (true === $debug) {
            $this->enableDebugging();
        }
        $this->init();

        if (! is_string($env) || empty($env)) {
            $err = "environment name must be a non empty string";
            throw new InvalidArgumentException($err);
        }
        $this->env = $env;
        
        $this->loadStandardPaths($root, $paths);
        $this->loadFileHandler($root);
    }

    /**
     * @return  AppKernel
     */
    public function init()
    {
        $this->enableFullErrorReporting();
        $this->registerAppfuelFaultHandler();
        if ($this->isDebuggingEnabled()) {
            $this->showErrors();   
        }
        else {
            $this->hideErrors();
        }

        return $this;
    }

    /**
     * @return  string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return  bool
     */
    public function isStarted()
    {
        return $this->isStarted;
    }

    /**
     * @return  AppKernel
     */
    public function markAsStarted()
    {
        $this->isStarted = true;
        return $this;
    }

    /**
     * @return  AppKernel
     */
    public function markAsNotStarted()
    {
        $this->isStarted = false;
        return $this;
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
     * @param   string  $rootPath
     * @return  FileHandler
     */
    public function createFileHandler($rootPath = null)
    {
        return new FileHandler($rootPath);
    }

    /**
     * @param   string  $root
     * @return  AppKernel
     */
    public function loadFileHandler($root)
    {
        $this->setFileHandler($this->createFileHandler($root));
        return $this;
    }

    /**
     * @param   string  $root
     * @param   array   $list
     * @return  PathCollection
     */
    public function createPathCollection($root, array $paths = null)
    {
        return new PathCollection($root, $paths);
    }

    /**
     * @return  PathCollection
     */
    public function getPathCollection()
    {
        return $this->paths;    
    }

    /**
     * @param   PathCollectionInterface $paths
     * @return  AppKernel
     */
    public function setPathCollection(PathCollectionInterface $paths)
    {
        $this->paths = $paths;
        return $this;
    }

    /**
     * @param   string  $root
     * @param   array   $list
     * @return  AppKernel
     */
    public function loadStandardPaths($root, array $list = null)
    {
        $paths = $this->getDefaultPaths();
        if (null !== $list) {
            $paths = array_merge($paths, $list);
        }

        $this->setPathCollection($this->createPathCollection($root, $paths));
        return $this;
    }

    /**
     * @return  array
     */
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
            'routes-cache'       => 'app/cache/routes.cache',
            'class-cache'        => 'app/cache/classes.cache.php',
            'class-cache-meta'   => 'app/cache/class.cache.meta',
            'container-cache'    => "app/cache/{$env}/container.cache.php",
            'app-packages'       => 'app/config/packages.php',
            'app-settings'       => "app/config/settings-{$env}.php", 
            'app-settings-cache' => "app/cache/{$env}/app-settings.cache.php", 
            'appfuel-src'        => "$vendor/src", 
            'appfuel-bin'        => "$vendor/bin",
        ); 
    }

    /**
     * @param   string  $name   name of the cache in the path collection
     * @return  AppKernel
     */
    public function loadClassCache($name)
    {
        $paths = $this->getPathCollection();
    }

    /**
     * @return  AppKernel
     */
    public function startUp()
    {
        $paths = $this->getPathCollection();
        $handler = $this->getFileHandler();
        $handler->throwExceptionOnFailure();

        $routes = $handler->readSerialized($paths->getRelative('routes-cache'));
        if (! $routes instanceof RouteCollectionInterface) {
            $err  = "the route cache must consist of a serailzed object that ";
            $err .= "implments Appfuel\\Route\\RouteCollectionInterface";
            throw new LogicException($err);
        }
        $this->setRouteDispatcher($this->createRouteDispatcher($routes));

        return $this;
    }

    /**
     * @param   MatchedRouteInterface
     * @return  array | Closure
     */
    public function getRouteDispatcher()
    {
        return $this->routeDispatcher;
    }

    /**
     * @param   RouterInterface $router
     * @return  AppKernel
     */
    public function setRouteDispatcher(RouteDispatcherInterface $dispatcher)
    {
        $this->routeDispatcher = $dispatcher;
        return $this;
    }

    /**
     * @param   RouteCollectionInterface $collection
     * @return  RouteDispatcher
     */
    public function createRouteDispatcher(RouteCollectionInterface $collection)
    {
        return new RouteDispatcher($collection);
    }
}
