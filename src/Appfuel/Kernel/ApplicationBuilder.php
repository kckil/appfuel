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
    Appfuel\Filesystem\FileHandler,
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
     * Decouples application paths from the classes that need them
     * @var PathCollectionInterface
     */
    protected $pathCollection = null;

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
    public function __construct($env)
    {
        if (! is_string($env) || empty($env)) {
            $err = "environment name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->env = $env;
    }

    /**
     * @return  string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return  string
     */
    public function getPathCollection()
    {
        return $this->pathCollection;
    }

    /**
     * @param   PathCollectionInterface $collection
     * @return  ApplicationBuilder
     */
    public function setPathCollection(PathCollectionInterface $collection)
    {
        $this->pathCollection = $collection;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isPathCollection()
    {
        return $this->pathCollection instanceOf PathCollectionInterface;
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
     * @param   string  $root   absolute path to the root of the app
     * @return  ApplicationBuilder
     */
    public function loadStandardPaths($root)
    {
        $env = $this->getEnv();
        $vendor = 'vendor/appfuel/appfuel';
        $settings = "app/cache/$env/app-settings.php";
        $list = array(
            'appfuel'       => $vendor,
            'appfuel-src'   => "$vendor/src",
            'appfuel-bin'   => "$vendor/bin",
            'app-settings'  => $settings
        );
        $collection = $this->createPathCollection($root, $list);
        $this->setPathCollection($collection);

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
        if (! $this->isPathCollection()) {
            $err = 'The path collection must be set before the file handler';
            throw new LogicException($err);
        }

        $paths = $this->getPathCollection();
        if ($paths->getRootPath() !== $fileHandler->getRootPath()) {
            $err  = 'The root path of the file handler and path collection ';
            $err .= 'must be the same';
            throw new LogicException($err);
        }

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
     * @return  ApplicationBuilder
     */
    public function loadFileHandler()
    {
        if (! $this->isPathCollection()) {
            $err  = 'The path collection must be set before the file handler ';
            $err .= 'is loaded';
            throw new LogicException($err);
        }
        $paths = $this->getPathCollection();

        $fileHandler = $this->createFileHandler($paths->getRootPath());
        $this->setFileHandler($fileHandler);

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

    /**
     * @return  bool
     */
    public function isSettings()
    {
        return $this->settings instanceof ArrayDataInterface;
    }

    /**
     * Appfuel builds a settings file based on configuration yml files during
     * the build process. Here we load that file into memory making sure it
     * is an array and used it as the application settings.
     *
     * @return  ApplicationBuilder
     */
    public function loadSettings(array $extra = null)
    {
        if (! $this->isFileHandler()) {
            $err = "The file handler must be set before settings are loaded";
            throw new LogicException($err);
        }
        $handler = $this->getFileHandler();

        if (! $this->isPathCollection()) {
            $err = "The path collection must be set before settings are loaded";
            throw new LogicException($err);
        }
        $paths = $this->getPathCollection();

        $file = $paths->getRelativePath('app-settings');
        $data = $handler->importScript($file);
        if (! is_array($data)) {
            $err = "settings -($file) must be a php file that returns an array";
            throw new LogicException($err);
        }

        if (null !== $extra) {
            $data = array_merge($data, $extra);
        }

        $this->setSettings(new ArrayData($data));
        return $this;
    }
}
