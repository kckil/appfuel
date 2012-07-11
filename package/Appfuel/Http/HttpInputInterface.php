<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

interface HttpInputInterface
{
    /**
     * @return string
     */
    public function getMethod();
   
    /**
     * @return bool
     */
    public function isPost();

    /**
     * @return string
     */
    public function isGet();

    /**
     * @return  bool
     */
    public function isPut();

    /**
     * @return  bool
     */
    public function isDelete();

    /**
     * @param   string  $key 
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($key, $default = null);

    /**
     * The params member is a general array that holds any or all of the
     * parameters for this request. This method will search on a particular
     * parameter and return its value if it exists or return the given default
     * if it does not
     *
     * @param   string  $key        used to find the label
     * @param   string  $type       type of parameter get, post, cookie etc
     * @param   mixed   $default    value returned when key is not found
     * @return  mixed
     */
    public function get($type, $key, $default = null);

    /**
     * Used to collect serval parameters based on an array of keys.
     * 
     * @param   array   $type   type of parameter stored
     * @param   array   $key    which request type get, post, argv etc..
     * @param   array   $isArray 
     * @return  ArrayData
     */
    public function collect($type, array $keys, $isArray = false);

    /**
     * @param   string  $type
     * @return  array
     */
    public function getAll($type = null);

    /**
     * Check for the direct ip address of the client machine, try for the 
     * forwarded address, check for the remote address. When none of these
     * return false
     * 
     * @return    int
     */
    public function getIp($isInt = true);
}
