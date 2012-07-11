<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\ClassLoader;

use InvalidArgumentException;

/**
 * The standard autoloader is an implementation that implements the technical 
 * interoperability standards for PHP 5.3 namespaces and class names.
 *
 *  Example which loads classes for anything in the lib dir
 *  $classLoader = new StandardAutoloader('/path/to/lib');
 *  $classLoader->register();
 * 
 */
class StandardAutoLoader implements AutoLoaderInterface
{
    /**
     * List of paths to search in
     * @var array
     */
    protected $pathList = array();

    /**
     * Flag used to determine if we should search the include path
     * @var bool
     */
    protected $isIncludePath = false;

    /**
     * @param   string  $path
     * @param   NamespaceParserInterface $parser
     * @return  StandardAutoLoader
     */
    public function __construct($path = null)
    {
        if (null !== $path && ! empty($path)) {
            if (is_string($path)) {
                $this->addPath($path);
            }
            else if (is_array($path)) {
                $this->addPaths($path);
            }
            else {
                $err = "path must be a string or an array";
                throw new InvalidArgumentException($err);
            }
        }
    }

    /**
     * @return  bool
     */
    public function isIncludePathEnabled()
    {
        return $this->isIncludePath;
    }

    /**
     * @return  StandardAutoLoader
     */
    public function enableIncludePath()
    {
        $this->isIncludePath = true;
        return $this;
    }

    /**
     * @return  StandardAutoLoader
     */
    public function disableIncludePath()
    {
        $this->isIncludePath = false;
        return $this;
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $namespace    
     * @param   string  absolute path to namespace
     * @return  StandardAutoLoader
     */
    public function addPath($path)
    {
        if (empty($path) || !is_string($path) || !($path = trim($path))) {
            $err = 'a path must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        if (! in_array($path, $this->pathList)) {
            $this->pathList[] = $path;
        }    
        return $this;
    }

    /**
     * @param   array   $path
     * @return  StandardAutoLoader
     */
    public function addPaths(array $paths)
    {
        foreach ($paths as $path) {
            $this->addPath($path);
        }

        return $this;
    }

    /**
     * @return  array
     */
    public function getPaths()
    {
        return $this->pathList;
    }

    /**
     * @return  StandardAutoLoader
     */
    public function clearPaths()
    {
        $this->pathList = array();
        return $this;
    }

    /**
     * @param   bool    $flag
     * @return  null
     */
    public function register($flag = false) 
    {
        $prepend = false;
        if (true === $flag) {
            $prepend = true;
        }

        spl_autoload_register(array($this, 'loadClass'), true, $prepend);
    }

    /**
     * @return null
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    /**
     * @param   string
     * @return  false   when file is not found 
     *          true    when class file is found and loaded
     */
    public function loadClass($class)
    {
        if (class_exists($class,false) || interface_exists($class,false)) {
            return true;
        }

        $path = NamespaceParser::parse($class);
        if (false === $path) {
            return false;
        }
                
        foreach ($this->pathList as $root) {
            $file = $root . DIRECTORY_SEPARATOR . $path;
            if (is_file($file)) {
                require $file;
                return true;
            }        
        }

        if ($this->isIncludePathEnabled() && 
            $file = stream_resolve_include_path($path)) {
            require $file;
            return true;
        }

        return false;
    }
}
