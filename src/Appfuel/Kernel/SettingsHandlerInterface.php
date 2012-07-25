<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

/**
 * Turns an application yaml file into a php array after merging the data
 * data from the files that the settings file imports.
 */
interface SettingsHandlerInterface
{
    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler();

    public function getYamlParser();

    /**
     * @param   string  
     * @return  array
     */
    public function resolve($file);
}
