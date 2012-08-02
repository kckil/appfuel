<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

use DomainException,    
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData;

interface HttpRequestInterface
{
    /**
     * @return  array
     */
    public function getAll();
    
    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function exists($key);

    /**
     * Check for the direct ip address of the client machine, try for the 
     * forwarded address, check for the remote address. When none of these
     * return false
     * 
     * @return    int
     */
    public function getClientIp($isInt = false);
}
