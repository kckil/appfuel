<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface;

/**
 */
interface RouteCollectorInterface
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param   string  $name
     * @return  RouteCollectorInterface
     */
    public function setFilename($name);

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader();

    /**
     * @param   FileReaderInterface $reader
     * @return  RouteCollectorInterface
     */
    public function setFileReader(FileReaderInterface $reader);


}
