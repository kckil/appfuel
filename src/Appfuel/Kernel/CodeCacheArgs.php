<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use OutOfRangeException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\Filesystem\FileHandler,
    Appfuel\Filesystem\FileHandlerInterface;

/**
 * A options used to control the CodeCacheHandler. The following keys are used
 * in the constructor:
 * classes:     list of classes to cache
 * cache-dir:   Path to the cache dir, should be absolute
 * cache-key:   filename of the cache
 * auto-reload: flag used to reload the cache, defaults to false
 * adaptive:    flag used to removed already declared classes, this
 *              has the side effect of changing the cache and meta filenames
 * ext:         file extension used for cache
 */
class CodeCacheArgs implements CodeCacheArgsInterface
{
    /**
     * List of classes to cached into a single file
     * @var array
     */
    protected $clasess = array();

    /**
     * @var string
     */
    protected $cacheDir = null;

    /**
     * @var string
     */
    protected $cacheKey = null;

    /**
     * Flag used to determine if the cache should be reloaded
     * @var bool
     */
    protected $isAutoReload = false;

    /**
     * Name of the cache file that hold all the classes
     * @var string
     */
    protected $filePath = null;

    /**
     * Name of the cache file that holds meta data about the cache
     * @var string
     */
    protected $metaPath = null;

    /**
     * @var FileHandlerInterface
     */
    protected $fileHandler = null;

    /**
     * @param   array $spec
     * @return  CodeCacheArgs
     */
    public function __construct(array $spec)
    {
        if (! isset($spec['cache-dir'])) {
            throw new OutOfBoundsException("-(cache-dir) arg is missing");
        }
        $cacheDir = $spec['cache-dir'];
        $this->setCacheDir($cacheDir);

        if (! isset($spec['cache-key'])) {
            throw new OutOfBoundsException("-(cache-key) arg is missing");
        }
        $cacheKey = $spec['cache-key'];
        $this->setCacheKey($cacheKey);

        if (isset($spec['auto-reload']) && true === $spec['auto-reload']) {
            $this->isAutoReload = true;
        }

        $ext = '.php';
        if (isset($spec['ext'])) {
            if (! is_string($spec['ext'])) {
                $err = "file extension must be a string";
                throw new OutOfRangeException($err);
            }
            $ext = $spec['ext'];
        }

        if (! isset($spec['classes'])) {
            throw new OutOfBoundsException("-(classes) arg is missing");
        }
        $classes = $spec['classes']; 
        
        $filePath = "{$cacheDir}/{$cacheKey}{$ext}";
        if (isset($spec['adaptive']) && true === $spec['adaptive']) {
            /* don't include already declared classes */
            $classes = array_diff($classes, $this->getPHPDeclared());
                   
            /* 
             * the cache is different depending on which classes 
             * are already declared.
             */
            $hash = substr(md5(implode('|', $classes)), 0, 5);
            $filePath = "{$cacheDir}/{$cacheKey}-{$hash}{$ext}";
        }

        $this->metaPath = "{$filePath}.meta";
        $this->filePath = $filePath;

        $this->setClasses($classes);

        if (! isset($spec['file-handler'])) {
            $fileHandler = new FileHandler();
        }
        else {
            $fileHandler = $spec['file-handler'];
            if (! $fileHandler instanceof FileHandlerInterface) {
                $err  = "file handler must implement -(Appfuel\\Filesystem";
                $err .= "\\FileHandlerInterface)";
                throw new OutOfBoundsException($err);
            }
        }
        $this->fileHandler = $fileHandler;
    }

    /**
     * @return  array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @return  string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @return  string
     */
    public function getCacheFilePath()
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getCacheMetaFilePath()
    {
        return $this->metaPath;
    }

    /**
     * @return  string
     */
    public function getCacheKey()
    {
        return $this->cacheKey;
    }

    /**
     * @return  bool
     */
    public function isAutoReload()
    {
        return $this->isAutoReload;
    }

    /**
     * @return  FileHandlerInterface
     */
    public function getFileHandler()
    {
        return $this->fileHandler;
    }

    /**
     * @return  array
     */
    public function getPHPDeclared()
    {
        $list = array_merge(get_declared_classes(), get_declared_interfaces());
        if (function_exists('get_declared_traits')) {
            $list = array_merge($list, get_declared_traits());
        }

        return $list;
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $key
     */
    protected function setCacheDir($dir)
    {
        if (! is_string($dir) || empty($dir)) {
            $err = "cache directory must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->cacheDir = $dir;
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $key
     */
    protected function setCacheKey($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "cache key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->cacheKey = $key;
    }

    /**
     * @throws  OutOfBoundsException
     * @param   array  $classes
     */
    protected function setClasses(array $classes)
    {
        foreach ($classes as $class) {
            if (! is_string($class) || empty($class)) {
                $err = "class entry in class list must be a non empty string";
                throw new OutOfBoundsException($err);
            }
        }

        $classes = array_unique($classes);
        sort($classes);
        $this->classes = $classes;
    }
}
