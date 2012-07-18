<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use DomainException,
    InvalidArgumentException;

/**
 * @see interface
 */
class FileFinder implements FileFinderInterface
{
    /**
     * Any path given to getPath will be relative to the root path
     * @var string
     */
    protected $root = null;

    /**
     * @param   string  $root
     * @return  FileFinder
     */
    public function __construct($path = null)
    {
        if (null !== $path) {
            $this->setRoot($path);
        }
    }

    /**
     * @return string
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return  bool
     */
    public function isRoot()
    {
        return is_string($this->root);
    }

    /**
     * @throws  InvalidArgumentException
     * @return  FileFinder
     */
    public function setRoot($path)
    {
        $this->root = $this->convertPath($path);
        return $this;
    }

    /**
     * @return  FileFinder
     */
    public function clearRoot()
    {
        $this->root = null;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isRootAbsolute()
    {
        return $this->isAbsolute($this->root);
    }

    /**
     * @return  bool
     */
    public function isAbsolute($path)
    {
        return is_string($path) && ! empty($path) && '/' === $path{0};
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  string
     */
    public function convertPath($path)
    {
        if (! is_string($path) && ! is_callable(array($path, '__toString'))) {
            $err  = "path must be a string or an object that implements ";
            $err .= "__toString";
            throw new InvalidArgumentException($err);
        }

        return (string) $path;
    }

    /**
     * Creates an absolute path by resolving base path (when it exists) root
     * path and the path passed in as a parameter
     *
     * @throws  DomainException
     * @throws  InvalidArgumentException 
     * @param   string  $path
     * @return  string
     */
    public function getPath($path = null)
    {
        $isRoot = $this->isRoot();
        if (null === $path && ! $isRoot) {
            $err  = "nothing useful can happen when no path is given ";
            $err .= "and no root path is set";
            throw new DomainException($err);
        }

        $root = $this->getRoot();
        if (null === $path && $isRoot) {
            return rtrim($root, "/");
        }

        $path = $this->convertPath($path);
        if ($isRoot) {
            $path = rtrim($root, "/") . '/' . ltrim($path, '/');   
        }

        return $path;
    }

    /**
     * @param   string  $path
     * @param   string  $path
     * @return  string
     */
    public function getPathBase($path, $suffix)
    {
        return basename($path, $suffix);
    }

    /**
     * @param   string  $path
     * @return  string
     */
    public function getDirPath($path)
    {
        return dirname($path);
    }

    /**
     * The last access time of a file
     *
     * @param   string $path
     * @return  Unix timestamp | false on failure
     */
    public function getLastModifiedTime($path)
    {
        return filemtime($path);
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * @param   string $path
     * @return  bool
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path)
    {
        return is_link($path);
    }
}
