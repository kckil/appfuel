<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException,
    InvalidArgumentException,
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\FileHandlerInterface,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Kernel\AppPathInterface,
    Appfuel\Kernel\Route\RouteManagerInterface,
    Appfuel\Kernel\DependencyInjection\DiManager,
    Appfuel\Kernel\DependencyInjection\DiManagerInterface;

class ApplicationBuilder implements ApplicationBuilderInterface
{
    /**
     * @var string
     */
    protected $rootPath = null;

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
     * List of application configuration settings
     * @var ArrayDataInterface
     */
    protected $settings = null;
 
    /**
     * @param   string  $root
     * @param   string  $env
     * @param   FileHandlerInterface $fileHandler
     * @return  Application
     */
    public function __construct($root, $env)
    {
        if (! is_string($root) || empty($root)) {
            $err = "the application root path must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if ('/' !== $root{0}) {
            $err = "the application root path must be an absolute path";
            throw new DomainException($err);
        }
        $this->rootPath = $root;

        if (! is_string($env) || empty($env)) {
            $err = "environment name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->env = $env;
    }

    /**
     * @return  string
     */
    public function getRootPath()
    {
        return $this->rootPath;
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
     * @return  ArrayDataInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param   ArrayDataInterface  $data
     * @return  ApplicationBuilder
     */
    public function setSettings(ArrayDataInterface $data)
    {
        $this->settings = $data;
        return $this;
    }
}
