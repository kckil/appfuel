<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Task;

use DomainException;

/**
 * Set the default timezone
 */
class PHPDefaultTimezoneTask extends StartupTask
{
    /**
     * @var   array
     */
    protected $keys = array('php-default-timezone');

    /**
     * @return  bool
     */
    public function execute()
    {
        $params = $this->getParamData();
        $tz = $params->get('php-default-timezone');
        if (is_string($tz) && ! empty($tz)) {
            date_default_timezone_set($tz);
            return true;
        }

        return false;
    }
}
