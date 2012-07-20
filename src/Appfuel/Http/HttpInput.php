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

class HttpInput implements HttpInputInterface
{
    /**
     * User input parameters separated by parameter type.
     * @var array
     */
    protected $params = array();

    /**
     * Method used for this request get, post, put, delete or cli
     * @var string
     */
    protected $method = null;

    /**
     * @var ValidationHandlerInterface
     */
    protected $handler = null;

    /**
     * @param   string  $method    
     * @param   array   $params
     * @return  HttpInput
     */
    public function __construct($method, array $params = array())
    {
        $this->setMethod($method);
        $this->setParams($params);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }
   
    /**
     * @return bool
     */
    public function isPost()
    {
        return 'post' === $this->method;
    }

    /**
     * @return string
     */
    public function isGet()
    {
        return 'get' === $this->method;
    }

    /**
     * @return  bool
     */
    public function isPut()
    {
        return 'put' === $this->method;
    }

    /**
     * @return  bool
     */
    public function isDelete()
    {
        return 'delete' === $this->method;
    }

    /**
     * @param   string  $key 
     * @param   mixed   $default
     * @return  mixed
     */
    public function getParam($key, $default = null)
    {
        return $this->get($this->getMethod(), $key, $default);
    }

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
    public function get($type, $key, $default = null)
    {
        if (! is_string($type) || empty($type)) {
            return $default;
        }
        $type = strtolower($type);

        if (! is_string($key) || empty($key)) {
            return $default;
        }

        if (! array_key_exists($key, $this->params[$type])) {
            return $default;
        }

        return $this->params[$type][$key];
    }

    /**
     * Used to collect serval parameters based on an array of keys.
     * 
     * @param   array   $type   type of parameter stored
     * @param   array   $key    which request type get, post, argv etc..
     * @param   array   $isArray 
     * @return  ArrayData
     */
    public function collect($type, array $keys, $isArray = false) 
    {
        $result = array();
        $notFound = '__AF_KEY_NOT_FOUND__';
        foreach ($keys as $key) {
            $value = $this->get($type, $key, $notFound);

            /* 
             * null or false could be accepted values and we need to
             * know when default comes back as true not not found vs 
             * the real value and default being the same
             */
            if ($value === $notFound) {
                continue;
            }
            $result[$key] = $value;
        }

        if (true === $isArray) {
            return $result;
        }

        return new ArrayData($result);
    }

    /**
     * @param   string  $type
     * @return  array
     */
    public function getAll($type = null)
    {
        if (null === $type) {
            return $this->params;
        }

        if (! is_string($type)) {
            $err = "param type -(http method) must be a string";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->params[$type])) {
            return false;
        }

        return $this->params[$type];
    }

    /**
     * Check for the direct ip address of the client machine, try for the 
     * forwarded address, check for the remote address. When none of these
     * return false
     * 
     * @return    int
     */
    public function getIp($isInt = true)
    {
        $client  = 'HTTP_CLIENT_IP';
        $forward = 'HTTP_X_FORWARDED_FOR';
        $remote  = 'REMOTE_ADDR'; 
        if (isset($_SERVER[$client]) && is_string($_SERVER[$client])) {
            $ip = $_SERVER[$client];
        }
        else if (isset($_SERVER[$forward]) && is_string($_SERVER[$forward])) {
            $ip = $_SERVER[$forward];
        }
        else if (isset($_SERVER[$remote]) && is_string($_SERVER[$remote])) {
            $ip = $_SERVER[$remote];
        }
        else {
            $ip = false;
        }

        if (false === $ip) {
            return false;
        }

        $isInt = ($isInt === false) ? false : true;
        $format = "%s";
        if (true === $isInt) {
            $format = "%u";
            $ip = ip2long($ip);
        }

        return sprintf($format, $ip);
    }

    /**
     * @param    string    $method
     * @return    null
     */
    protected function setMethod($method)
    {
        if (! is_string($method) || empty($method)) {
            $err = "input method must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->method = strtolower($method);
    }

    /**
     * @param    array    $params
     * @return    null
     */
    protected function setParams(array $params)
    {
        $result = array();
        foreach ($params as $type => $data) {
            if (! is_string($type) || empty($type)) {
                $err = "param type must be a non empty string";
                throw new DomainException($err);
            }

            if (! is_array($data)) {
                $datatype = gettype($data);
                $err = "data for -($type) must be an array: -($datatype) given";
                throw new DomainException($err);
            }

            $type = strtolower($type);
            $result[$type] = $data;
        }

        $this->params = $result;
    }
}
