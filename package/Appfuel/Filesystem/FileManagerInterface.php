<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

interface FileManagerInterface
{
    /**
     * @param   string  $rootPath
     * @param   bool    $isBase
     * @return  FileFinder
     */
    public function createFileFinder($rootPath = null, $isBase = true);
}
