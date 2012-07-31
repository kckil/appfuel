<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

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
    protected $paths = array();

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $root
     * @return  PathCollection
     */
    public function __construct($root, array $list = null)
    {
        $this->setRoot($root);
        if (null !== $list) {
            $this->load($list);
        }
    }

    /**
     * @return  string
     */
    public function getRoot()
    {
        return $this->root;
    }
    
    /**
     * @param   string  $path
     * @return  null
     */
    public function setRoot($path)
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

    /**
     * @return  array
     */
    public function getMap()
    {
        return $this->paths;
    }

    /**
     * @param   array   $list
     * @return  PathCollection
     */
    public function set(array $list)
    {
        $this->clear();
        return $this->load($list);
    }

    /**
     * @param   array   $list
     * @return  PathCollection
     */
    public function load(array $list)
    {
        foreach ($list as $name => $path) {
            $this->add($name, $path);
        }

        return $this;
    }

    /**
     * @return  PathCollection
     */
    public function clear()
    {
        $this->paths = array();
        return $this;
    }

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $name
     * @param   string  $path
     * @return  PathCollection
     */
    public function add($name, $path)
    {
        if (! is_string($name) || empty($name)) {
            $err = "path name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (! is_string($path) || empty($path)) {
            $err = "path for -($name) must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $root = $this->getRoot();
        if ('/' === $path{0}) {
            $err  = "path for -($name) must not be  absolute, since all paths ";
            $err .= "are to be under the root path -($root)";
            throw new DomainException($err);
        }

        $this->paths[$name] = $path;
        return $this;
    }

    /**
     * @param   string  $name
     * @return  string | false
     */
    public function get($name)
    {
      return "{$this->getRoot()}/{$this->getRelative($name)}";
    }

    /**
     * @return  bool
     */
    public function exists($name)
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
    public function getRelative($name)
    {
        if (! $this->exists($name)) {
            throw new DomainException("path -($name) was not found");
        }

        return $this->paths[$name];
    }
}
