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
    Appfuel\Filesystem\FileHandlerInterface,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Kernel\AppPathInterface,
    Appfuel\Kernel\Route\RouteManagerInterface,
    Appfuel\Kernel\DependencyInjection\DiManagerInterface;

class Application implements ApplicationInterface
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
     * @var PathCollectionInterface
     */
    protected $pathCollection = null;
   
    /**
     * @var FileHandlerInterface
     */
    protected $fileHandler = null;

    /**
     * @param   string  $root
     * @param   PathCollectionInterface  $p paths used by this app
     * @param   bool    $debug
     * @return  Application
     */
    public function __construct($env, PathCollectionInterface $p, $debug = null)
    {
        $this->env = $env;
        $this->pathCollection = $p;

        if (true === $debug) {
            $this->isDebug = true;
        }
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
    public function isDebuggingEnabled()
    {
        return  $this->isDebug;
    }

    /**
     * @return  string
     */
    public function getPathCollection()
    {
        return $this->pathCollection;
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
     * @return  Application
     */
    public function setFileHandler(FileHandlerInterface $fileHandler)
    {
        $paths = $this->getPathCollection();
        if ($paths->getRootPath() !== $fileHandler->getRootPath()) {
            $err  = 'The root path for both path collection and file handler ';
            $err .= 'must be the same';
            throw new LogicException($err);
        }

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
     * @return  Application
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
     * @return  Application
     */
    public function setDependencyInjectionManager(DiManagerInterface $manager)
    {
        $this->diManager = $manager;
        return $this;
    }
}
