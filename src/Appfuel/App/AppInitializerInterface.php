<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

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
}
