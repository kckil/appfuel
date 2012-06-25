<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use LogicException,
    DomainException,
    RecursiveIteratorIterator,
    RecursiveDirectoryIterator,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileFinder;

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
     * @param   string  $filename
     * @param   FileReaderInterface $reader
     * @return  RouteCollector
     */
    public function __construct($filename = null)
    {
        if (null !== $filename) {
            $this->setFilename($filename);
        }
    }

    /**
     * @param   $list   list of directories to search 
     * @return  array
     */
    public function collect(array $dirs)
    {
        $master = array();
        $finder = new FileFinder(null, false);
        $reader = new FileReader($finder);

        $filename = $this->getFilename();
        foreach ($dirs as $dir) {
            if (! is_string($dir) || empty($dir)) {
                $err  = "dir path to actions must be a non empty string";
                throw new DomainException($err);
            }
            $topDir = new RecursiveDirectoryIterator($finder->getPath($dir));
            foreach (new RecursiveIteratorIterator($topDir) as $file) {
                if ($filename !== $file->getFilename()) {
                    continue;
                }
                $fullPath = $file->getRealPath();
                $routes = $reader->import($fullPath);
                if (! is_array($routes)) {
                    $type = gettype($routes);
                    $err  = "routes file at -($fullPath) must return an array ";
                    $err .= "of route specifications -($type) given instead";
                    throw new LogicException($err);
                }
                foreach ($routes as $key => $spec) {
                    if (isset($master[$key])) {
                        $err  = "route key -($key) already exists at ";
                        $err .= "({$master[$key]['namespace']})";
                        throw new LogicException($err);
                    }
                    if (! is_array($spec) || $spec === array_values($spec)) {
                        $err = "route spec -($key) must be an assoc array";
                        throw new LogicException($err);
                    }
                    $master[$key] = $spec;
                }
            }
        }

        return $master;
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
}
