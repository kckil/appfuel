<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException;

/**
 * set the php include path
 */
class PHPPathTask extends StartupTask
{
    /**
     * @var array
     */
    protected $keys = array(
        'php-include-path',
        'php-include-path-action',
        
    );

    /**
     * @return  null
     */
    public function execute()
    {
        $params = $this->getParamData();
        $paths  = $params->get('php-include-path');
        if (empty($paths)) {
            return false;
        }

        /* a single path was passed in */
        if (is_string($paths) && ! empty($paths)) {
            $path = $paths;
        } else if (is_array($paths) && ! empty($paths)) {
            $path= implode(PATH_SEPARATOR, $paths);
        } else {
            $err = 'include path can only be a string or an array of strings';
            throw new DomainException($err);
        }

        $action = $params->get('php-include-path-action', 'append');
        if (! in_array($action, array('append', 'prepend', 'replace'), true)) {
            $err = "action must be -(append, prepend, replace)";
            throw new DomainException($err);
        }

        $includePath = get_include_path();
        if ('append' === $action) {
            $path = $includePath . PATH_SEPARATOR . $path;
        } else if ('prepend' === $action) {
            $path .= PATH_SEPARATOR . $includePath;
        }

        set_include_path($path);
        return true;
    }
}
