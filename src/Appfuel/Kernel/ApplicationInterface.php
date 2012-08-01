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

interface ApplicationInterface
{
    /**
     * @return  string
     */
    public function getEnv();

    /**
     * @return  bool
     */
    public function isDebuggingEnabled();

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler();

    /**
     * @return  FileHandlerInterface
     */
    public function getEventDispatcher();

    /**
     * @param   FileHandlerInterface    $fileHandler
     * @return  ApplicationInterface
     */
    public function setEventDispatcher(EventDispatcherInterface $dispatcher);

    /**
     * @return  DiManagerInterface
     */
    public function getDependencyInjectionManager();

    /**
     * @param   DiManagerInterface  $manager
     * @return  ApplicationInterface
     */
    public function setDependencyInjectionManager(DiManagerInterface $manager);
}
