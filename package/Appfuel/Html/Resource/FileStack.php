<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * Holds a list of files categorized by type. When a file is added it is always 
 * prepended to the top of the stack. This is done because, in an html resource
 * module files have dependencies and dependencies are always added before 
 * their dependent files.
 */
class FileStack implements FileStackInterface
{
    /**
     * @var array
     */
    protected $files = array();

    /**
     * @param    array    $files
     * @return    FileStack
     */
    public function __construct(array $files = null)
    {
        if (null !== $files) {
            $this->load($files);
        }
    }

    /**
     * @return    array
     */
    public function getAll()
    {
        return $this->files;
    }

    /**
     * @return    array
     */
    public function getTypes()
    {
        return array_keys($this->files);
    }

    /**
     * @param    string    $type
     * @return    array | false when type does not exist
     */
    public function get($type, $path = null)
    {
        if (! is_string($type) || ! isset($this->files[$type])) {
            return array();
        }

        $files = $this->files[$type];
        if (is_string($path)) {
            foreach ($files as $index => &$file) {
                $file = "$path/$file";
            }
        }
            
        return $files;
    }

    /**
     * @param    string    $type
     * @param    array    $list
     * @return    array
     */
    public function diff($type, array $list)
    {
        if (! $this->isType($type)) {
            return $list;
        }

        return array_diff($list, $this->files[$type]);
    }

    /**
     * @param    string    $type
     * @return    bool
     */
    public function isType($type)
    {
        if (! is_string($type) || ! isset($this->files[$type])) {
            return false;
        }

        return true;
    }

    /**
     * @param    string    $type
     * @param    string    $file
     * @return    bool
     */
    public function isFile($type, $file)
    {
        if (! is_string($file) || empty($file) || ! $this->isType($type)) {
            return false;
        }

        return in_array($file, $this->files[$type], true);
    }

    /**
     * @param    string    $type
     * @param    string    $file
     * @return    PackageFileList
     */
    public function add($type, $file)
    {
        if (! is_string($type) || empty($type)) {
            $err = 'file type must be a none empty string';
            throw new InvalidArgumentException($err);
        }

        if (! is_string($file) || empty($file)) {
            $err = 'file path must be a none empty string';
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->files[$type])) {
            $this->files[$type] = array($file);
            return $this;
        }

        if (! in_array($file, $this->files[$type])) {
            $this->files[$type][] = $file;
        }
            
        return $this;
    }

    /**
     * @param    array    $list
     * @return    FileStack
     */
    public function load(array $list)
    {
        if ($list === array_values($list)) {
            $err = 'file list must be an associative array of type => array';
            throw new InvalidArgumentException($err);
        }

        foreach ($list as $type => $files) {
            if (is_string($files)) {
                $this->add($type, $files);
                continue;
            }

            if (! is_array($files)) {
                $err = 'array structure must be type=>string or type=>array';
                throw new InvalidArgumentException($err);
            }

            foreach ($files as $file) {
                    $this->add($type, $file);
            }
        }

        return $this;
    }

    /**
     * @param    FileStackInterface $stack
     * @return    FileStack
     */
    public function loadStack(FileStackInterface $stack)
    {
        $types = $stack->getTypes();
        foreach ($types as $type) {
            $files = $stack->get($type);
            if (empty($files)) {
                continue;
            }
            foreach ($files as $file) {
                $this->add($type, $file);
            }
        }

        return $this;
    }

    /**
     * @param    array    $list
     * @return    PackageFileList
     */
    public function set(array $list)
    {
        $this->clear();
        $this->load($list);
        return $this;
    }

    /**
     * @return    PackageFileLIst
     */
    public function clear($type = null)
    {
        if (null === $type) {
            $this->files = array();
            return $this;
        }

        if (is_string($type) && isset($this->files[$type])) {
            $this->files[$type] = array();
        }

        return $this;
    }
}
