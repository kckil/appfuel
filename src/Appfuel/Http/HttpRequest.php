<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

use LogicException,
    DomainException,    
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData;

/**
 * This class was dervived from Symfony 2 code base 
 * symfony/src/Symfony/Component/HttpFoundation/Request.php
 * commit# 6ac8e7308dd3730c50662a70e635dccab1394560
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
     * Http method used to make the request. This is always lower-case
     * @var string
     */
    protected $method = null;

    /**
     * @var string
     */
    protected $requestUri = null;

    /**
     * @var string
     */ 
    protected $baseUrl = null;

    /**
     * @var string
     */ 
    protected $basePath = null;
 
    /**
     * @var string
     */ 
    protected $pathInfo = null;
   
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
     * @param   string  $str
     * @return  string
     */
    public static function normalizeQueryString($str)
    {
        if ('' === $str) {
            return '';
        }

        $parts = array();
        foreach(explode('&', $str) as $param) {
            /*
             * Ignore useless delimiters, e.g. "x=y&".
             * Also ignore pairs with empty key, even if there was a value,
             * e.g. "=value", as such nameless values can not be retrieved
             */
            if ('' === $param || '=' === $param[0]) {
                continue;
            }
            $data = explode('=', $param, 2);
            
            /*
             * GET parameters, are are submitted form a HTML form, encode spaces
             * as "+" by default (as defined in enctype application/x-www-form
             * -urlencoded). PHP also converts "+" to spaces when filling the
             * global _GET or when using the parse_str. This is why we use
             * urldecode and them normalize to RFC 3986 with rawurlencode
             */
            if (isset($data[1])) {
                $parts[] = rawurlencode(urldecode($data[0])) . '=' .
                           rawurlencode(urldecode($data[1]));
            }
            else {
                $parts[] = rawurlencode(urldecode($data[0]));
            }
        }

        return implode('&', $parts);
    }

    /**
     * Returns a normailzed query string for the Request. It provides 
     * consistent escaping. Unlike Symfony we do not sort the query string
     *
     * @return  string | null
     */
    public function getQueryString()
    {
        $str = static::normalizeQueryString($this->get('QUERY_STRING'));
        return '' === $str ? null : $str;
    }

    /**
     * @return  string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->baseUrl = $this->prepareBaseUrl();
        }

        return $this->baseUrl;
    }

    /**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     * 
     * @return  string
     */
    public function getBasePath()
    {
        if (null === $this->basePath) {
            $this->basePath = $this->prepareBasePath();
        }

        return $this->basePath;
    }

    /**
     * Returns the path being requested relative to the executed script.
     * (not urldecoded)
     * 
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * htpp://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = $this->preparePathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * @return  string
     */
    public function getRequestUri()
    {
        if (null !== $this->requestUri) {
            return $this->requestUri;
        }

        $uri = '';
        if ($this->exists('REQUEST_URI')) {
            $uri = $this->get('REQUEST_URI');
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            /* HTTP proxy reqs setup request uri with scheme and host 
             * [and port] + the url path, only use url path
             */
            if (strpos($uri, $schemeAndHttpHost) === 0) {
                $uri = substr($uri, strlen($schemeAndHttpHost));
            }
        }

        $this->requestUri = $uri;
        return $uri;
    }

    /** 
     * @throws  LogicException 
     * @return  string 
     */ 
    public function getMethod() 
    {
        if (null !== $this->method) {
            return $this->method;
        }

        if ($this->exists('X-HTTP-METHOD-OVERRIDE')) { 
            $method = $this->get('X-HTTP-METHOD-OVERRIDE'); 
        } 
        else if ($this->exists('REQUEST_METHOD')) { 
            $method = $this->get('REQUEST_METHOD'); 
        } 
        else { 
            $err = 'http request method was not set'; 
            throw new LogicException($err); 
        } 

        if (! is_string($method) || empty($method)) {
            $err = 'http reqest method must be a non empty string';
            throw new LogicException($err);
        }

        $this->method = strtoupper($method);

        return $this->method;  
    }

    /**
     * @return  bool
     */
    public function isMethodSafe()
    {
        return in_array($this->getMethod(), array('GET', 'HEAD'));
    }

    /**
     * @return  bool
     */
    public function isSecure()
    {
        $isHttps = false;
        if ($this->exists('HTTPS')) {
            $value = strtolower($this->get('HTTPS'));
            if ('on' === $value || 1 == $value) {
                $isHttps = true;
            }
            
            return $isHttps;
        }
        
        if (self::isProxyTrusted() && $this->exists('SSL_HTTPS')) {
            $value = strtolower($this->get('SSL_HTTPS'));
            if ('on' === $value || 1 == $value) {
                $isHttps = true;
            }
        }

        return $isHttps;
    }

    /**
     * @return  string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * @return  string
     */
    public function getPort()
    {
        if (self::isProxyTrusted() && $this->exists('X-Forwarded-Port')) {
            return $this->get('X-Forwarded-Port');
        }

        return $this->get('SERVER_PORT');
    }

    /**
     * @return  string|null
     */
    public function getUser()
    {
        return $this->get('PHP_AUTH_USER');
    }

    /**
     * @return  string|null
     */
    public function getPassword()
    {
        return $this->get('PHP_AUTH_PW');
    }

    /**
     * Returns a user name and, optionally, scheme specific information about
     * how to gain authorization to access the server
     *
     * @return  string
     */
    public function getUserInfo()
    {
        $info = $this->getUser();
        $pass = $this->getPassword();
        if ('' != $pass) {
            $info .= ":$pass";
        }

        return $info;
    }
    
    /**
     * @return  string
     */
    public function getHost()
    {
        if (self::isProxyTrusted() && $host = $this->getForwardedHost()) {
            $items = explode(',', $host);
            $host  = trim($items[count($items) -1]);
        }
        else {
            if (! $host = $this->get('HTTP_HOST')) {
                if (! $host = $this->get('SERVER_NAME')) {
                    $host = $this->get('SERVER_ADDR', '');
                }
            }
        }

        // remove port number from host
        $host = preg_replace('/:\d+$/', '', $host);

        return trim(strtolower($host));
    }

    /**
     * Returns the HTTP host being requested, appending the port name if 
     * its none standard.
     *
     * @return  string  
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port  = $this->getPort();

        $host = $this->getHost();
        if (('http' === $scheme && 80 == $port) || 
            ('https' === $scheme && 443 == $port)) {
            return $host;
        }

        return "$host:$port";
    }

    /**
     * @return  string
     */
    public function getSchemeAndHttpHost()
    {
        $str = "{$this->getScheme()}://";
        
        $auth = $this->getUserInfo();
        if ('' != $auth) {
            $str .= "$auth@";
        }

        return "{$str}{$this->getHttpHost()}";
    }

    /**
     * @return  bool
     */
    public function isForwarded()
    {
        return $this->exists('HTTP_X_FORWARDED_HOST');
    }

    /**
     * @return  string | null
     */
    public function getForwardedHost()
    {
        return $this->get('HTTP_X_FORWARDED_HOST');
    }

    /**
     * @return  string
     */
    public function getScriptName()
    {
        return $this->get('SCRIPT_NAME');
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

    protected function prepareBaseUrl()
    {
        $filename = basename($this->get('SCRIPT_FILENAME'));

        if ($filename === basename($this->get('SCRIPT_NAME'))) {
            $baseUrl = $this->get('SCRIPT_NAME');
        }
        else if ($filename === basename($this->get('PHP_SELF'))) {
            $baseUrl = $this->get('PHP_SELF');
        }
        else if ($filename === basename($this->get('ORIG_SCRIPT_NAME'))) {
            $baseUrl = $this->get('ORIG_SCRIPT_NAME');
        }
        else {
            // backtrack up the script_filename to find the portion matching
            $path = $this->get('PHP_SELF', '');
            $file = $this->get('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $idx  = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$idx];
                $baseUrl = '/'. $seg . $baseUrl;
                ++$idx;
            } while (($last > $idx) && 
                     (false !== ($pos = strpos($path, $baseUrl))) &&
                     (0 != $pos));
        }

        // does the base url have anything in common with the request uri
        $uri = $this->getRequestUri();
        $prefix = $this->getUrlencodedPrefix($uri, $baseUrl);
        if ($baseUrl && false !== $prefix) {
            // full $baseUrl matches
            return rtrim($prefix, '/');
        }
        
        $prefix = $this->getUrlencodedPrefix($uri, dirname($baseUrl));
        if ($baseUrl && false !== $prefix) {
            // directory portion of the base url matches
            return rtrim($prefix, '/');
        }

        $truncatedUri = $uri;
        if (($pos = strpos($uri, '?')) !== false) {
            $truncatedUri = substr($uri, 0, $pos);
        }

        $basename = basename($baseUrl);
        if (empty($basename) || 
            ! strpos(rawurldecode($truncatedUri), $basename)) {
            // no match whatsoever set it to empty string
            return '';
        }

       /*
        * If using mod_rewrite or ISAPI_Rewrite strip the script filename
        * out of the baseUrl. $pos !== 0 makes sure it in not matching
        * a value from PATH_INFO or QUERY_STRING
        */
        if ((strlen($uri) >= strlen($baseUrl)) &&
            ((false !== ($pos = strpos($uri, $baseUrl))) &&
            ($post !== 0))) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }
        
        return rtrim($baseUrl, '/');
    }

    /**
     * @return string
     */
    protected function prepareBasePath()
    {
        $filename = basename($this->get('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath);
        }

        return rtrim($basePath, '/');
    }

   /**
     * @return string
     */
    protected function preparePathInfo()
    {
        $baseUrl = $this->getBaseUrl();

        if (null === ($requestUri = $this->getRequestUri())) {
            return '/';
        }

        $pathInfo = '/';

        // Remove the query string from REQUEST_URI
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((null !== $baseUrl) && 
            (false === ($pathInfo = substr($requestUri,strlen($baseUrl))))) {
            // If substr() returns false PATH_INFO is set to an empty string
            return '/';
        } elseif (null === $baseUrl) {
            return $requestUri;
        }

        return (string) $pathInfo;
    }

    /*
     * Returns the prefix as encoded in the string when the string starts with
     * the given prefix, false otherwise.
     *
     * @param string $string The urlencoded string
     * @param string $prefix The prefix not encoded
     *
     * @return string|false The prefix as it is encoded in $string, or false
     */
    private function getUrlencodedPrefix($string, $prefix)
    {
        if (0 !== strpos(rawurldecode($string), $prefix)) {
            return false;
        }

        $len = strlen($prefix);

        if (preg_match("#^(%[[:xdigit:]]{2}|.){{$len}}#", $string, $match)) {
            return $match[0];
        }

        return false;
    }
}
