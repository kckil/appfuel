<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Component\Filesystem;

use DomainException,
    InvalidArgumentException;

class FileSystemFactory
{
    /**
     * @param   string  $path
     * @return  FileFinder
     */
    public static function createFileFinder($path = null)
    {
        return new FileFinder($path);
    }

    /**
     * @param   string | FileFinderInterface
     * @return  FileReader
     */
    public static function createFileReader($path = null)
    {
        return new FileReader(self::filterFileFinder($path));
    }

    /**
     * @param   string | FileFinderInterface
     * @return  FileReader
     */
    public static function createFileWriter($path = null)
    {
        return new FileWriter(self::filterFileFinder($path));
    }

    /**
     * @param   string  | FileFinderInterface
     * @return  FileHandler
     */
    public static function createFileHandler($path = null)
    {
        return new FileHandler(self::filterFileFinder($path));
    }

    /**
     * @param   mixed   $obj
     * @return  bool
     */
    public static function isFileFinder($obj)
    {
        return $obj instanceof FileFinderInterface;
    }

    /**
     * @param   string | FileFinderInterface
     * @return  FileFinderInterface
     */
    protected static filterFileFinder($path = null)
    {
        if (self::isFileFinder($path)) {
            return $path;
        }
            
        return self::createFileFinder($path);
    }
}
