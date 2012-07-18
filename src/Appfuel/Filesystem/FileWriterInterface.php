<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Filesystem;

interface FileWriterInterface
{
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
     * @param   string  $data
     * @param   string  $path
     * @param   string  $mode
     * @param   int     $length
     * @return    
     */
    public function putContent($data, $path, $flags = 0);
}
