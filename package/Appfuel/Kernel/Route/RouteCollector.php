<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
    RunTimeException,
    RecursiveIteratorIterator,
    RecursiveDirectoryIterator,
    Appfuel\Filesystem\FileReader,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Regex\RegexPattern,
    Appfuel\Regex\RegexPatternInterface;

/**
 * Recursively traverse action directories collecting their route details
 */
class RouteCollector implements RouteCollectorInterface
{
    /**
     * Name of the php file that holds the route details
     * @var string
     */
    protected $filename = 'route-details.php';

    /**
     * Used to filter and validate the regex to be well formed.
     * @var RegexPatternInterface
     */
    protected $regex = null;

    /**
     * @param   string  $filename
     * @param   RegexPatternInterface $regex
     * @return  RouteCollector
     */
    public function __construct($file=null, RegexPatternInterface $regex=null)
    {
        if (null !== $file) {
            $this->setFilename($file);
        }

        if (null == $regex) {
            $regex = new RegexPattern();
        }

        $this->setRegEx($regex);
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
     * @return  RegexPatternInterface
     */
    public function getRegEx()
    {
        return $this->regex;
    }

    /**
     * @param   RegexPatternInterface   $regex
     * @return  RouteCollector
     */
    public function setRegEx(RegexPatternInterface $regex)
    {
        $this->regex = $regex;
        return $this;
    }

    /**
     * @param   $list   list of directories to search 
     * @return  array
     */
    public function collect(array $dirs)
    {
        $collection = array();
        $finder = new FileFinder(null, false);
        $reader = new FileReader($finder);

        $filename = $this->getFilename();
        foreach ($dirs as $dir) {
            if (! is_string($dir) || empty($dir)) {
                $err  = "dir path to actions must be a non empty string";
                throw new RunTimeException($err);
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
                    throw new RunTimeException($err);
                }
                foreach ($routes as $key => $spec) {
                    if (! is_string($key) || empty($key)) {
                        $err  = "route key must be a non empty string: failed ";
                        $err .= "-($fullPath)";
                        throw new RunTimeException($err);
                    }
                    if (isset($collection[$key])) {
                        $err  = "route key -($key) already exists at ";
                        $err .= "({$collection[$key]['namespace']})";
                        throw new RunTimeException($err);
                    }
                    if (! is_array($spec) || $spec === array_values($spec)) {
                        $err = "route spec -($key) must be an assoc array";
                        throw new RunTimeException($err);
                    }
                
                    $collection[$key] = $this->preProcess($key,$spec,$fullPath);
                }
            }
        }

        return $collection;
    }

    /**
     * @param   string  $key    route key
     * @param   array   $spec   route specification
     * @param   string  $fullPath   absolute path to routes file
     * @return  array
     */
    protected function preProcess($key, array $spec, $fullPath)
    {
        /* add route key to the specification */
        $spec['route-key'] = $key;
       
        /*
         * you know better than I so don't check the regex
         */
        if (isset($spec['compiled-pattern'])) {
            return $spec;
        }
 
        /*
         * This route has no pattern to process so do nothing
         */
        if (! isset($spec['pattern']) && ! isset($spec['pattern-map'])) {
            return $spec;
        }
        
        $map = array(
            'default' => null,
            'get'     => null,
            'post'    => null,
            'put'     => null,
            'delete'  => null
        );

        if (isset($spec['pattern'])) {
            $regex = $spec['pattern'];
            $map['default'] = $this->validateRegex($regex, $key, $fullPath);
        }
        else if (isset($spec['pattern-map'])) {
            $regexMap = $spec['pattern-map'];
            if (! is_array($regexMap)) {
                $type = gettype($regexMap);
                $err  = "regex pattern spec for must be a string or an array ";
                $err  = " type given was -($type) for -($key, $fullPath)";
                throw new RunTimeException($err);
            }

            foreach ($regexMap as $key => $regexStr) {
                if (! array_key_exists($key, $map)) {
                    continue;
                }

                $map[$key] = $this->validateRegex($regexStr, $key, $fullPath);
            }
        }

        $spec['compiled-pattern'] = $map;
        
        return $spec;
    }

    /**
     * @throws  RunTimeException
     * @param   string  $pattern
     * @param   string  $key
     * @param   string  $fullpath
     * @return  string
     */
    protected function validateRegex($raw, $key, $fullPath)
    {
        $modifiers = "";
        if (is_array($raw)) {
            $modifiers = isset($raw[1]) ? $raw[1] : null;
            if (! is_string($modifiers) || empty($modifiers)) {
                $modifiers = "";
            }
            $raw = current($raw);
        }

        $pattern = $this->regex->filter($raw, $modifiers);
        if (false === $pattern) {
            $msg  = $this->regex->getError();
            $err  = "failed to compile route: malformed regex -($msg): ";
            $err .= "-($key, $fullPath): pattern -(" . $raw . ")";
            throw new RunTimeException($err);
        }

        return $pattern;
    }
}
