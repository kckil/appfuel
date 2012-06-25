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
class RouteCollector implements RouteCollectorInterface
{
    /**
     * Name of the php file that holds the route details
     * @var string
     */
    protected $filename = 'route-details.php';

    /**
     * Used to import the route details file into memory
     * @var FileReaderInterface
     */
    protected $reader = null;

    /**
     * @param   string  $filename
     * @param   FileReaderInterface $reader
     * @return  RouteCollector
     */
    public function __construct($filename = null, 
                                FileReaderInterface $reader = null)
    {
        if (null !== $filename) {
            $this->setFilename($filename);
        }

        if (null === $reader) {
            $reader = new FileReader(new FileFinder());
        }
        $this->setFileReader($reader);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param   string  $name
     * @return  RouteCollector
     */
    public function setFilename($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "filename must be a non empty string";
            throw new DomainException($err);
        }

        $this->filename = $name;
        return $this;
    }

    /**
     * @return  FileReaderInterface
     */
    public function getFileReader()
    {
        return $this->reader;
    }

    /**
     * @param   FileReaderInterface $reader
     * @return  RouteCollector
     */
    public function setFileReader(FileReaderInterface $reader)
    {
        $this->reader = $reader;
        return $this;
    }
}
