<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\ClassLoader;

/**
 * The autoloader decouples the parsing of the namespace by using a parser.
 * Its only responsibilities are checking if the file exists against a list
 * of paths.
 */
interface AutoLoaderInterface
{
    /**
     * @return  bool
     */
    public function isIncludePathEnabled();

    /**
     * @return  StandardAutoLoaderInterface
     */
    public function enableIncludePath();

    /**
     * @return  StandardAutoLoaderInterface
     */
    public function disableIncludePath();

    /**
     * @param    string    $namespace    
     * @param    string    absolute path to namespace
     * @return  StandardAutoLoaderInterface
     */
    public function addPath($path);

    /**
     * @return  array
     */
    public function getPaths();

    /**
     * @param   bool    $flag
     * @return  null
     */
    public function register($prepend = false);

    /**
     * @param   string
     * @return  null
     */
    public function loadClass($class);
}
