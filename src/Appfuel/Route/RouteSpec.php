<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use LogicException,
    OutOfBoundsException,
    InvalidArgumentException;

class RouteSpec implements RouteSpecInterface
{
    /**
     * @var string  $key
     */
    protected $key = null;

    /**
     * @return  string
     */
    protected $pattern = null;

    /**
     * Action controller used by the framework once a match is satified
     * @var string
     */
    protected $controller = null;

    /**
     * @var string
     */
    protected $controllerMethod = 'execute';

    /**  
     * Used to aid in generating urls for this route as well as naming
     * regex captures so you don't have to use (?<name>) syntax.
     * @var array
     */ 
    protected $params = array();

    /**
     * List of parameter defaults which will be used when a parameter is not
     * present
     * @var array
     */
    protected $defaults = array();

    /**
     * Used to enforce an http scheme
     * @var string
     */
    protected $uriScheme = null;

    /**
     * @var string
     */
    protected $httpMethod = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct(array $spec)
    {
        if (! isset($spec['key'])) { 
            $err = '-(key) is expected but not given'; 
            throw new OutOfBoundsException($err); 
        } 
        $this->setKey($spec['key']);

        if (! isset($spec['pattern'])) { 
            $err = '-(pattern) regex pattern is expected but not given'; 
            throw new OutOfBoundsException($err); 
        } 
        $this->setPattern($spec['pattern']);

        if (isset($spec['controller'])) { 
            $this->setController($spec['controller']);
        }

        if (isset($spec['controller-method'])) {
            $this->setControllerMethod($spec['controller-method']);        
        }

        if (isset($spec['params'])) {
            $this->setParams($spec['params']);
        }

        if (isset($spec['http-method'])) {
            $this->setHttpMethod($spec['http-method']);
        }

        if (isset($spec['uri-scheme'])) {
            $this->setUriScheme($spec['uri-scheme']);
        }
    }

    /**
     * @return  string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return  string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return  string
     */
    public function getControllerMethod()
    {
        return $this->controllerMethod;
    }

    /**
     * @return  array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param   string  $method
     * @return  bool
     */
    public function isHttpMethodAllowed($method = null)
    {
        if (null !== $method && ! (is_string($method) && !empty($method))) {
            $err = "http method must be null or a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (empty($this->httpMethod)) {
            return true;
        }

        foreach ($this->httpMethod as $specMethod) {
            if (strtoupper($specMethod) === strtoupper($method)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return  string | null
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }

    /**
     * @param   string  $scheme
     * @return  bool
     */
    public function isUriSchemeAllowed($scheme = null)
    {
        if (null !== $scheme && ! (is_string($scheme) && ! empty($scheme))) {
            $err = "uri scheme must be null or a non empty string";
            throw new InvalidArgumentException($err);
        }
        
        if (empty($this->uriScheme)) {
            return true;
        }

        return strtolower($this->uriScheme) === strtolower($scheme);
    }

    /**
     * @return  string | null
     */
    public function getUriScheme()
    {
        return $this->uriScheme;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setKey($key)
    {
        if (! $this->isValidString($key)) {
            $err = "route key must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->key = $key;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setPattern($pattern)
    {
        if (! $this->isValidString($pattern)) {
            $err = "uri pattern must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->pattern = $pattern;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setController($ctrl)
    {
        if (! (is_string($ctrl) && ! empty($ctrl)) && ! is_callable($ctrl)) {
            $err = "controller must be callable or a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->controller = $ctrl;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setControllerMethod($name)
    {
        if (! $this->isValidString($name)) {
            $err = "controller method name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->controllerMethod = $name;
    }

    /**
     * @param   array   $params
     * @return  null
     */
    protected function setParams(array $params)
    {
        foreach ($params as $param) {
            if (! $this->isValidString($param)) {
                $err = 'route parameter must be a non empty string';
                throw new OutOfBoundsException($err);
            }
        }

        $this->params = $params;
    }

    /**
     * @param  string  $name
     * @return  null
     */
    protected function setHttpMethod($name)
    {
        if (is_string($name) && ! empty($name)) {
            $methods = explode('|', $name);
            
        } 
        else if (is_array($name)) {
            $methods = $name;
        }
        else {
            $err = "http method must be null, array or a non empty string";
            throw new InvalidArgumentException($err);

        }

        $this->httpMethod = $methods;
    }

    /**
     * @param  string  $name
     * @return  null
     */
    protected function setUriScheme($name)
    {
        if (! $this->isValidString($name)) {
            $err = "http scheme name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->uriScheme = strtolower($name);
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    protected function isValidString($key)
    {
         if (! is_string($key) || empty($key)) {
            return false;
        }
    
        return true;
    }
}
