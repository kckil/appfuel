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

/**
 * This class was dervived from code of Symfony 2 
 * 
 * Code is subject to MIT license
 * http://http://symfony.com/doc/current/contributing/code/license.html
 * Copyright (c) 2004-2012 Fabien Potencier
 */
class HttpRequest implements HttpRequestInterface
{
    /**
     * @var string
     */
    protected static $isProxyTrusted = false;

    /**
     * List of variables form the $_SERVER super global
     * @var array
     */
    protected $server = array();

    /**
     * @var   string
     */
    protected $requestUri = null;
    
    /**
     * @param   array   $params
     * @return  HttpRequest
     */
    public function __construct(array $params)
    {
        $this->server = $params;
    }

    /**
     * @return null
     */
    public static function markProxyAsTrusted()
    {
        self::$isProxyTrusted = true;
    }

    /**
     * @return  null
     */
    public static function markProxyAsUnsafe()
    {
        self::$isProxyTrusted = false;
    }

    /**
     * @return  bool
     */
    public static function isProxyTrusted()
    {
        return self::$isProxyTrusted;
    }

    /** 
     * @throws  LogicException 
     * @return  string 
     */ 
    public function getMethod() 
    { 
        if ($this->exists('HTTP_X_HTTP_METHOD_OVERRIDE')) { 
            $method = $this->get('HTTP_X_HTTP_METHOD_OVERRIDE'); 
        } 
        else if ($this->exists('REQUEST_METHOD')) { 
            $method = $this->get('REQUEST_METHOD'); 
        } 
        else { 
            $err = 'http request method was not set'; 
            throw new LogicException($err); 
        } 
 
        return $method;  
    }

    /**
     * @return  bool
     */
    public function isSecure()
    {
        $isHttps = false;
        if ($this->exists('HTTPS')) {
            $value = strtolower($this->get('HTTPS'));
            if ('on' === $value || 1 === $value) {
                $isHttps = true;
            }
        }
        
        if ($this->exists('SSL_HTTPS')) {
            $value = strtolower($this->get('SSL_HTTPS'));
            if ('on' === $value || 1 === $value) {
                $isHttps = true;
            }
        }

        return $isHttps;
    }

    /**
     * @return  array
     */
    public function getAll()
    {
        return $this->server;
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
         if (! is_string($key) || empty($key)) {
            $err = "server key must be a non empty string";
            throw new InvalidArgumentException($err);
        }


        if (! array_key_exists($key, $this->server)) {
            return $default;
        }

        return $this->server[$key];
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function exists($key)
    {
         if (! is_string($key) || empty($key)) {
            $err = "server key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        return array_key_exists($key, $this->server);      
    }


    /**
     * Check for the direct ip address of the client machine, try for the 
     * forwarded address, check for the remote address. When none of these
     * return false
     * 
     * @return    int
     */
    public function getClientIp($isInt = false)
    {
        $ip = false;
        if (self::isProxyTrusted()) {
            if ($this->exists('HTTP_CLIENT_IP')) {
                $ip = $this->get('HTTP_CLIENT_IP');
            }
            else if ($this->exists('HTTP_X_FORWARDED_FOR')) {
                $clientList = explode(',', $this->get('HTTP_X_FORWARDED_FOR'));

                foreach ($clientList as $data) {
                    $ipAddress = trim($data);
                    if (false !== filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                        $ip = $ipAddress;
                        break;
                    }
                }
            }
            else if ($this->exists('REMOTE_ADDR')) {
                $ip = $this->get('REMOTE_ADDR');
            }
        }
        else {
            $ip = $this->get('REMOTE_ADDR');
        }

        if (false === $ip) {
            return false;
        }

        $format = "%s";
        if (true === $isInt) {
            $format = "%u";
            $ip = ip2long($ip);
        }

        return sprintf($format, $ip);
    }
}
