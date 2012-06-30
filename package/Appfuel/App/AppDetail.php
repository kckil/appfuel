<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use DomainException;

/**
 * Handle path information for the following
 * <base-path>  : absolute path to the applications root directory
 * www            : the web root directory
 * bin            : cli scripts
 * test            : unit test bootrapping and supporting files
 * package        : php source code
 * resource        : clientside resource files js,css,html,phtml etc...
 * routes        : route specification files 
 * config        : config files 
 * datasource    : mappings for database, webservices, files etc..
 * build        : system generated files    
 * 
 * Allows the application dir structure change without changing the
 * kernel code.
 */
class AppDetail implements AppDetailInterface
{
    /**
     * Absolute path to the root directory of the application
     * @var   string
     */
    protected $base = null;

    /**
     * Flag used to determine if an exception will be thrown when a path is
     * not found when a call to getPath is used.
     * @var bool
     */
    protected $isStrict = true;

    /**
     * List of all the main directories and files used by appfuel
     * @var array
     */
    protected $paths = array(
        'app-www'               => 'www',
        'app-bin'               => 'bin',
        'app-test'              => 'test',
        'app-src'               => 'package',
        'app-resource'          => 'resource',
        'app-datasource'        => 'datasource',
        'app-dir'               => 'app',
        'app-build'             => 'app/build',
        'app-config-settings'   => 'app/config-settings.php',
        'app-config-build'      => 'app/build/config.json',
        'app-routes-build'      => 'app/build/routes.json',
        'app-url-groups'        => 'app/url-groups.php',
    );

    /**
     * @param   string  $basePath
     * @return  AppDetail
     */
    public function __construct(array $spec)
    {
        if (! isset($spec['app-root'])) {
            $err = "base path -(base) is required and must be set";
            throw new DomainException($err);
        }
        $this->setBasePath($spec['app-root']);

        $reserved = array('app-root', 'app-dir', 'app-build');
        foreach ($spec as $key => $path) {
            if (in_array($key, $reserved, true)) {
                continue;
            }
            $this->addPath($key, $path);
        }
    }

    /**
     * @return  string
     */
    public function getBasePath()
    {
        return $this->base;
    }

    /**
     * @return  bool
     */
    public function isStrict()
    {
        return $this->isStrict;
    }

    /**
     * @return  AppPath
     */
    public function enableStrictMode()
    {
        $this->isStrict = true;
        return $this;
    }

    /**
     * @return  AppPath
     */
    public function disableStrictMode()
    {
        $this->isStrict = false;
        return $this;
    }

    /**
     * @param   string  $name
     * @return  string | false
     */
    public function getPath($name, $isAbsolute = true)
    {
        if (! is_string($name) || ! isset($this->paths[$name])) {
            if (! $this->isStrict()) {
                return false;
            }
            $type = gettype($name);
            $name = ('string' !== $type) ? $name : "<can not display>";
            throw new DomainException("path -($name, $type) was not found");
        }

        $base = '';
        if (true === $isAbsolute) {
            $base = $this->getBasePath() . DIRECTORY_SEPARATOR;
        }

        return "{$base}{$this->paths[$name]}";
    }

    /**
     * @param   string  $path
     * @return  null
     */
    protected function setBasePath($path)
    {
        if (! is_string($path)) {
            $err = "base path must be a non empty string";
            throw new DomainException($err);
        }

        if (DIRECTORY_SEPARATOR !== $path{0}) {
            $err = "base path must be an absolute path";
            throw new DomainException($err);
        }
        
        $this->base = $path;
    }

    protected function addPath($key, $path)
    {
        if (! is_string($key) || empty($key)) {
            $err = "path key must be a non empty string";
            throw new DomainException($err);
        }

        if (! is_string($path) || empty($path)) {
            $err = "path for -($key) must be a non empty string";
            throw new DomainException($err);
        }

        $base = $this->getBasePath();
        if (DIRECTORY_SEPARATOR === $path{0}) {
            $err  = "path for -($key) must not be  absolute, since all paths ";
            $err .= "are to be under the app-root -($base)";
            throw new DomainException($err);
        }

        $this->paths[$key] = $path;
    }
}
