<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\Kernel;

use DomainException;

/**
 * Set the default timezone
 */
class PHPDefaultTimezoneTask extends StartupTask
{
    /**
     * @return  PHPDefaultTimezoneTask
     */
    public function __construct()
    {
        $this->setRegistryKeys(array('php-default-timezone'    => null));
    }

    /**
     * @param   array   $params
     * @return  null
     */
    public function execute(array $params = null)
    {
        $msg = '';
        if (isset($params['php-default-timezone'])) {
            $zone = $params['php-default-timezone'];
            if (! is_string($zone) || empty($zone)) {
                $err = 'timezone must be a non empty string';
                throw new DomainException($err);
            }
            date_default_timezone_set($zone);
            $msg = "default timezone was set to -($zone)";
        }

        $this->setStatus($msg);
    }
}
