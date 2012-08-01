<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use Appfuel\Filesystem\FileHandlerInterface,
    Appfuel\Filesystem\PathCollectionInterface;

interface AppKernelInterface
{
    /**
     * @return  AppKernelInterface
     */
    public function init();

    /**
     * @return  string
     */
    public function getEnv();

    /**
     * @return  AppKernelInterface
     */
    public function showErrors();

    /**
     * @return  AppKernelInterface
     */
    public function hideErrors();

    /**
     * @return  AppKernelInterface
     */
    public function disableErrorReporting();

    /**
     * @return  AppKernelInterface
     */
    public function enableFullErrorReporting();

    /**
     * @param   int $level
     * @return  AppKernelInterface
     */
    public function setErrorReporting($level);

    /**
     * @return  AppKernelInterface
     */
    public function enableDebugging();

    /**
     * @return  AppKernelInterface
     */
    public function disableDebugging();

    /**
     * @return  bool
     */
    public function isDebuggingEnabled();

    /**
     * @param   callable    $handler
     * @return  AppKernelInterface
     */
    public function registerExceptionHandler($handler);

    /**
     * @param   callable    $handler
     * @param   int         $errorTypes
     * @return  AppKernelInterface
     */
    public function registerErrorHandler($handler, $types = null);

    /**
     * @return  AppKernelInterface
     */
    public function registerAppFuelFaultHandler();

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler();

    /**
     * @param   FileHandlerInterface    $fileHandler
     * @return  AppKernelInitializer
     */
    public function setFileHandler(FileHandlerInterface $fileHandler);

    /**
     * @param   string  $rootPath
     * @return  FileHandler
     */
    public function createFileHandler($rootPath = null);
}
