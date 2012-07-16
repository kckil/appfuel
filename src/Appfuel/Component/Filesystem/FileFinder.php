<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Component\Filesystem;

use DomainException,
    InvalidArgumentException;

/**
 * @see interface
 */
class FileFinder implements FileFinderInterface
{
    /**
     * @var string
     */
    protected $basePath = null;

    /**
     * @param   string  $path
     * @return  FileFinder
     */
    public function __construct($path = null)
    {
        if (null !== $path) {
            $this->setBasePath($path);
        }
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @return  bool
     */
    public function isBasePath()
    {
        return is_string($this->basePath);
    }

    /**
     * @throws  InvalidArgumentException
     * @return  FileFinder
     */
    public function setBasePath($path)
    {
        $this->basePath = $this->convertPath($path);
        return $this;
    }

    /**
     * @return  bool
     */
    public function isAbsolute()
    {
        return $this->isBasePath() && '/' === $this->basePath{0};
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  string
     */
    public function convertPath($path)
    {
        if (! (is_string($path) || 
            is_object($path) && is_callable($path, '__toString'))) {
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
        $isBase = $this->isBasePath();
        if (null === $path && ! $isBase) {
            $err  = "nothing useful can happen when no path is given ";
            $err .= "and no base path was set";
            throw new DomainException($err);
        }

        $base = $this->getBasePath();
        if (null === $path && $isBase) {
            return $base;
        }

        $path = $this->convertPath($path);
        if ($isBase) {
            $path = $base . DIRECTORY_SEPARATOR . $path;   
        }

        return $path;
    }

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException 
     * @param   string  $path
     * @return  string | false if path does not exist
     */
    public function getExistingPath($path = null)
    {
        $full = $this->getPath($path);
        if (! file_exists($full)) {
            return false;
        }

        return $full;
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function exists($path)
    {
        return file_exists($this->getPath($path);
    }

    /**
     * @param   string $path
     * @return  bool
     */
    public function isWritable($path)
    {
        return is_writable($this->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path)
    {
        return is_readable($this->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path)
    {
        return is_file($this->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path)
    {
        return is_dir($this->getPath($path));
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path)
    {
        return is_link($this->getPath($path));
    }
}
