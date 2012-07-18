<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

interface FileReaderInterface
{
    /**
     * used to indicate a file could not be read
     */
    const READ_FAILURE = '__AF_FILESYSTEM_READ_FAILURE__';

    /**
     * @return  FileFinderInterface
     */
    public function getFileFinder();

    /**
     * @param   FileFinderInterface $finder
     * @return  FileReader
     */
    public function setFileFinder(FileFinderInterface $finder);

    /**
     * @param   string  $path
     * @param   bool    $isOnce
     * @return  mixed
     */
    public function importScript($path, $isOnce = false);

    /**
     * @param   string  $path
     * @param   bool    $isOnce
     * @return  mixed
     */
    public function includeScript($path, $isOnce = false);

    /**
     * @param   string  $path
     * @param   bool    $isAssoc
     * @param   int     $depth
     * @param   int     $options
     * @return  array | object | false
     */
    public function readJson($path, $assoc=true, $depth=512, $options=0);

    /**
     * @return  string
     */
    public function getLastJsonError();

    /**
     * @param   string  $path
     * @return  string | false when does not exist
     */
    public function read($path);

    /**
     * @param   string  $path
     * @return  string
     */
    public function readSerialized($path);

    /**
     * @param   string  $file
     * @param   int     $flags = 0
     * @return  array | false when not found
     */
    public function readIntoArray($path, $flags = 0);
}
