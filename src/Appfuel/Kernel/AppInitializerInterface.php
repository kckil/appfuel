<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

/**
 * Application initialization involves error display, error level, register
 * error and exception handling and moving configuration data into the 
 * settings registry.
 */
interface AppInitializerInterface
{
    /**
     * This represents the environment the application is currently running in.
     *
     * @return  string
     */
    public function getEnv();

    /**
     * @return  AppInitializerInterface
     */
    public function showErrors();

    /**
     * @return  AppInitializerInterface
     */
    public function hideErrors();

    /**
     * @return  AppInitializerInterface
     */
    public function disableErrorReporting();

    /**
     * @return  AppInitializerInterface
     */
    public function enableFullErrorReporting();

    /**
     * @param   int $level
     * @return  AppInitializerInterface
     */
    public function setErrorReporting($level);

    /**
     * @return  AppInitializerInterface
     */
    public function enableDebugging();

    /**
     * @param   callable    $handler
     * @param   int         $errorTypes
     * @return  AppInitializerInterface
     */
    public function registerErrorHandler($handler, $errorTypes = null);

    /**
     * @param   callable    $handler
     * @return  AppInitializerInterface
     */
    public function registerExceptionHandler($handler);

    /**
     * @return  AppInitializerInterface
     */
    public function registerAppfuelFaultHandler();
}
