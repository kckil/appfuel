<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException,
    InvalidArgumentException;

class PathCollection implements PathCollectionInterface
{
    /**
     * Absolute path to the root directory of the application
     * @var   string
     */
    protected $root = null;

    /**
     * List of all the main directories and files used by appfuel
     * @var array
     */
    protected $paths = array(
        'www'               => 'www',
        'bin'               => 'bin',
        'test'              => 'test',
        'src'               => 'src',
        'app'               => 'app',
        'cache'             => 'app/cache',
        'config'            => 'app/config',
        'vendor'            => 'vendor'
    );

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $basePath
     * @return  AppPath
     */
    public function __construct($root, array $list)
    {
        $this->setRootPath($root);

        foreach ($list as $name => $path) {
            $this->addPath($name, $path);
        }
    }

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $name
     * @param   string  $path
     * @return  PathCollection
     */
    public function addPath($name, $path)
    {
        if (! is_string($name) || empty($name)) {
            $err = "path name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (! is_string($path) || empty($path)) {
            $err = "path for -($name) must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $root = $this->getRootPath();
        if ('/' === $path{0}) {
            $err  = "path for -($name) must not be  absolute, since all paths ";
            $err .= "are to be under the root path -($root)";
            throw new DomainException($err);
        }

        $this->paths[$name] = $path;
        return $this;
    }

    /**
     * @return  string
     */
    public function getRootPath()
    {
        return $this->root;
    }

    /**
     * @return  bool
     */
    public function isPath($name)
    {
        if (! is_string($name) || ! isset($this->paths[$name])) {
            return false;
        }

        return true;
    }

    /**
     * @throws  DomainException when path is not found
     *
     * @param   string  $name
     * @return  string
     */
    public function getRelativePath($name)
    {
        if (! $this->isPath($name)) {
            throw new DomainException("path -($name) was not found");
        }

        return $this->paths[$name];
    }

    /**
     * @param   string  $name
     * @return  string | false
     */
    public function getPath($name)
    {
      return "{$this->getRootPath()}/{$this->getRelativePath($name)}";
    }

    /**
     * @param   string  $path
     * @return  null
     */
    protected function setRootPath($path)
    {
        if (! is_string($path) || empty($path)) {
            $err = "root path must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if ('/' !== $path{0}) {
            $err = "root path must be an absolute path";
            throw new DomainException($err);
        }
        
        $this->root = $path;
    }
}
