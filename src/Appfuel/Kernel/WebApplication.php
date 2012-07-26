<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use InvalidArgumentException;

/**
 */
class WebApplication extends Application implements WebApplicationInterface
{
    /**
     * @param   array   $list
     * @return  AppInitializer
     */
    public function restrictWebAccessTo(array $list, $msg)
    {
        foreach ($list as $ip) {
            if (! is_string($ip) || empty($ip)) {
                $err  = "each item in the list must be a non empty string ";
                $err .= "that represents an ip address";
                throw new DomainException($err);
            }
        }

        if (! is_string($msg)) {
            $err = "script restriction message must be a string";
            throw new InvalidArgumentException($err);
        }

        if (isset($_SERVER['HTTP_CLIENT_IP']) ||
            isset($_SERVER['HTTP_X_FORWARDED_FOR']) ||
            ! in_array(@$_SERVER['REMOTE_ADDR'], $list)) {
            header('HTTP/1.0 403 Forbidden');
            exit($msg);
        }
    }
}
