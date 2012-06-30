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
class AppPath implements AppPathInterface
{
    /**
     * Absolute path to the root directory of the application
     * @var   string
     */
    protected $base = null;

    /**
     * List of all the main directories and files used by appfuel
     * @var array
     */
    protected $paths = array(
        'www'               => 'www',
        'bin'               => 'bin',
        'test'              => 'test',
        'src'               => 'package',
        'resource'          => 'resource',
        'datasource'        => 'datasource',
        'app-dir'           => 'app',
        'app-build'         => 'app/build',
        'config-settings'   => 'app/config-settings.php',
        'config-build'      => 'app/build/config.json',
        'routes-build'      => 'app/build/routes.json',
        'url-groups'        => 'app/url-groups.php',
    );

    /**
     * @param   string  $basePath
     * @return  AppPath
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
            $this->add($key, $path);
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
     * @param   string  $name
     * @return  string | false
     */
    public function get($name, $isAbsolute = true, $isStrict = true)
    {
        if (! is_string($name) || ! isset($this->paths[$name])) {
            if (false === $isStrict) {
                return false;
            }
            $type = gettype($name);
            $name = ('string' === $type) ? $name : "<can not display>";
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

    protected function add($key, $path)
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
