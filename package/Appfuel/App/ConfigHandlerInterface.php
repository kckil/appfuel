<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use Appfuel\Filesystem\FileReaderInterface;

interface ConfigHandlerInterface
{
    /**
     * @param   string  $path
     * @param   string  $path
     * @return  bool
     */
    public function loadFile($path, $isReplace = true);

    /**
     * Read a json file or any php file. When php files are used it expects 
     * the file will return an array of config data
     *
     * @param   string  $path   relative path to the config file
     * @return  array
     */
    public function getFileData($path);
}
