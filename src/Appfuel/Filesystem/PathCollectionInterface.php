<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

use DomainException;

interface PathCollectionInterface
{
    /**
     * @return  string
     */
    public function getRoot();

    /**
     * @throws  DomainException 
     * @throws  InvalidArgumentException
     * 
     * @param   array   $list
     * @return  PathCollectionInterface
     */
    public function set(array $list);

    /**
     * @throws  DomainException 
     * @throws  InvalidArgumentException
     * 
     * @param   array   $list
     * @return  PathCollectionInterface
     */
    public function load(array $list);

    /**
     * @return  PathCollectionInterface
     */
    public function clear();

    /**
     * @throws  DomainException             when path is an abolute path
     * @throws  InvalidArgumentException    when $name, $path are non empty 
     *                                      strings
     * @param   string  $name
     * @param   string  $path
     * @return  PathCollectionInterface
     */
    public function add($name, $path);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function exists($name);

    /**
     * @throws  DomainException             when $name is not found
     * @throws  InvalidArgumentException    when $name is a non empty string
     *
     * @param   string  $name
     * @return  string
     */
    public function get($name);

    /**
     * @throws  DomainException             when $name is not found
     * @throws  InvalidArgumentException    when $name is a non empty string
     *
     * @param   string  $name
     * @return  string
     */
    public function getRelative($name);

}
