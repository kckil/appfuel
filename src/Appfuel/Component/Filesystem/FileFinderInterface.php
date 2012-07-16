<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Component\Filesystem;

/**
 * A generic representation of a file path. Its primary goal is to answer 
 * questions about the file like: does it exist, is it readable etc ... 
 */
interface FileFinderInterface
{
    /**
     * @return string
     */
    public function getBasePath();

    /**
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  FileFinderInterface
     */
    public function setBasePath($path);

    /**
     * @return  bool
     */
    public function isBasePath();

    /**
     * @return bool
     */
    public function isAbsolute();

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  string
     */
    public function getPath($path = null);

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  string
     */
    public function getExistingPath($path = null);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function exists($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isWritable($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isReadable($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isFile($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isDir($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function isLink($path = null);
}
