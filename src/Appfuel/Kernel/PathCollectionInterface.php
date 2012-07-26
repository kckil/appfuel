<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException;

interface PathCollectionInterface
{
    /**
     * @return  string
     */
    public function getRootPath();

    /**
     * @throws  DomainException             when path is an abolute path
     * @throws  InvalidArgumentException    when $name, $path are non empty 
     *                                      strings
     * @param   string  $name
     * @param   string  $path
     * @return  PathCollectionInterface
     */
    public function addPath($name, $path);

    /**
     * @param   string  $name
     * @return  bool
     */
    public function isPath($name);

    /**
     * @throws  DomainException             when $name is not found
     * @throws  InvalidArgumentException    when $name is a non empty string
     *
     * @param   string  $name
     * @return  string
     */
    public function getRelativePath($name);

    /**
     * @throws  DomainException             when $name is not found
     * @throws  InvalidArgumentException    when $name is a non empty string
     *
     * @param   string  $name
     * @return  string
     */
    public function getPath($name);
}
