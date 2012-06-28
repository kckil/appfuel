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
     * @param   string
     */
    protected $base = null;

    /**
     * List of all the main directories and files used by appfuel
     * @var array
     */
    protected $paths = [
        'app-www'                   => 'www',
        'app-bin'                   => 'bin',
        'app-test'                  => 'test',
        'app-src'                   => 'package',
        'app-resource'              => 'resource',
        'app-datasource'            => 'datasource',
        'app-dir'                   => 'app',
        'app-build'                 => 'app/build',
        'app-config-settings'       => 'app/config-settings.php',
        'app-config-build-file'     => 'app/build/config.json',
    ];

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

        foreach ($this->paths as $key => &$path) {
            if (!isset($spec[$key]) || 'app'===$key || 'app-build'===$key) {
                continue;
            }
            $specPath = $spec[$key];
            if (! $this->isValid($specPath)) {
                throw new DomainException("-($key) is not a valid path");
            }
            $path = $specPath;
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
    public function getPath($name, $isAbsolute = true)
    {
        if (! is_string($name) || ! isset($this->paths[$name])) {
            return false;
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
        if (! $this->isValid($path)) {
            $err = "base path must be a non empty string";
            throw new DomainException($err);
        }

        if (DIRECTORY_SEPARATOR !== $path{0}) {
            $err = "base path must be an absolute path";
            throw new DomainException($err);
        }
        
        $this->base = $path;
    }

    /**
     * @param   string  $path
     * @return  bool
     */
    protected function isValid($path)
    {
        if (! is_string($path) || empty($path)) {
            return false;
        }

        return true;
    }
}
