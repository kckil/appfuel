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
     * @return  FileFinderInterface
     */
    public function getFinder();

    /**
     * @param   FileFinderInterface $finder
     * @return  FileReader
     */
    public function setFinder(FileFinderInterface $finder);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function require($path);

    /**
     * @param   string  $path
     * @return  bool
     */
    public function requireOnce($path);

    /**
     * @param   string  $path
     * @param   bool    $isAssoc
     * @param   int     $depth
     * @param   int     $options
     * @return  array | object
     */
    public function decodeJsonAt($path, $assoc=true, $depth=512, $options=0);

    /**
     * @return  string
     */
    public function getLastJsonError();

    /**
     * @throws  InvalidArgumentException
     * @param   string  $path
     * @param   int     $offset
     * @param   int     $max
     * @return  string | false when does not exist
     */
    public function getContents($path = null, $offset = null, $max = null);

    /**
     * @throws  InvalidArgumentException
     * @param   string  $file
     * @param   int     $flags = 0
     * @return  array | false when not found
     */
    public function getContentsAsArray($file, $flags = 0);
}
