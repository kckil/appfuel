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
    public function createPathCollection($root, array $paths)
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
            'framework-cache'    => 'app/cache/framework.cache.php',
            'container-cache'    => "app/cache/{$env}/container.cache.php", 
            'app-settings'       => "app/config/settings-{$env}.yml", 
            'app-settings-cache' => "app/cache/{$env}/app-settings.cache.php", 
            'appfuel-src'        => "$vendor/src", 
            'appfuel-bin'        => "$vendor/bin",
        ); 
    }
}
