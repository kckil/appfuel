<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileReaderInterface;

/**
 * Creates a list of routes by recursively searching a list of directories for
 * route-details.php and adding them to an array.
 */
class RouteListBuilder implements RouteListBuilderInterface
{
    /**
     * Name of the file that holds a list of route details for a namespace
     * @var     string
     */
    protected $file = 'route-details.php';

    /**
     * List of route details found by the builder
     * @var array
     */
    protected $routes = null;

    /**
     * Used to read the route file into php memory
     * @var FileReaderInterface
     */
    protected $reader = null;

    /**
     * @param   FileReaderInterface $reader
     * @param   string $file
     * @return  RouteListBuilder
     */
    public function __construct(FileReaderInterface $reader=null, $file=null)
    {
        if (null === $reader) {
            $finder = new FileFinder('package');
            $reader = new FileReader(new FileFinder('package'));
        }
        $this->setFileReader($reader);
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
     * @return  RouteListBuilder
     */
    public function setFileReader(FileReaderInterface $reader)
    {
        $this->reader = $reader;
        return $this;
    }

    /**
     * @return    string
     */
    public function getRouteDetailFile()
    {
        return $this->file;
    }

    /**
     * @param    string    $file
     * @return    RouteListBuilder
     */
    public function setRouteDetailFile($file)
    {
        if (! is_string($file) || empty($file)) {
            $err = "route detail file must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->file = $file;
    }
}
