<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException;

/**
 * Calls ini_set on the key value pairs in the config registry
 */
class PHPIniTask extends StartupTask 
{
    /**
     * @var array
     */
    protected $keys = array('php-ini');

    /**
     * @return bool
     */
    public function execute()
    {
        $params = $this->getParamData();
        $data = $params->get('php-ini');
        if (empty($data)) {
            return false;
        }

        if (! is_array($data) || $data === array_values($data)) {
            $err = 'php ini settings must be an associative array of ';
            $err = 'ini varname => ini newvalue';
            throw new DomainException($err);
        }

        foreach ($data as $varname => $newvalue) {
            if (! is_string($varname) || empty($varname)) {
                $err = "ini name must non empty string: at index -($count)";
                throw new DomainException($err);
            }

            if (! is_scalar($newvalue)) {
                $err = "ini value must be a scalar value: at index -($count)";
                throw new DomainException($err);
            }

            ini_set($varname, $newvalue);
        }

        return true;
    }
}
