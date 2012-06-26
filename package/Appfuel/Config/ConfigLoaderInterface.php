<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Config;

/**
 * Loads config data into the configuration registry. The data can be from a
 * php file that returns an array or a json file, the data can also be just 
 * an array.
 */
interface ConfigLoaderInterface
{
    /**
     * @return  FileReaderInterface
     */
    public function getFileReader();

    /**
     * @param   string  $path
     * @param   string  $path
     * @return  bool
     */
    public function loadFile($path, $isReplace = true);

    /**
     * @param   array   $data
     * @return  null
     */
    public function load(array $data);

    /**
     * @param   array   $data
     * @return  null
     */
    public function set(array $data);

    /**
     * @param   string  $path
     * @return  array | false
     */
    public function getFileData($path);
}
