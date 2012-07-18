<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;


interface FileFinderInterface
{
    /**
     * @return string
     */
    public function getRoot();

    /**
     * @return  bool
     */
    public function isRoot();

    /**
     * @throws  InvalidArgumentException
     * @return  FileFinder
     */
    public function setRoot($path);

    /**
     * @return  bool
     */
    public function isRootAbsolute();

    /**
     * @return  bool
     */
    public function isAbsolute($path);

    /**
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @return  string
     */
    public function convertPath($path);

    /**
     * Creates an absolute path by resolving base path (when it exists) root
     * path and the path passed in as a parameter
     *
     * @throws  DomainException
     * @throws  InvalidArgumentException 
     * @param   string  $path
     * @return  string
     */
    public function getPath($path = null);

    /**
     * @param   string  $path
     * @param   string  $path
     * @return  string
     */
    public function getPathBase($path, $suffix = null);

    /**
     * @param   string  $path
     * @return  string
     */
    public function getDirPath($path);

    /**
     * The last access time of a file
     *
     * @param   string $path
     * @return  Unix timestamp | false on failure
     */
    public function getLastModifiedTime($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function exists($path);

    /**
     * @param   string $path
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
    public function isLink($path);
}
