<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use InvalidArgumentException;

class MatchedRoute implements MatchedRouteInterface
{
    /**
     * @var string  $key
     */
    protected $key = null;

    /**
     * Action controller used by the framework once a match is satified
     * @var string
     */
    protected $controller = null;

    /**  
     * @var array
     */ 
    protected $captures = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct($key, $ctrl, $method, array $captures = null)
    {
        $this->setKey($key);
        $this->setMethod($method);
        $this->setController($ctrl);

        if (null !== $captures) {
            $this->setCaptures($captures);
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
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return  string
     */
    public function getMethod()
    {
        return $this->method;
    }
    
    /**
     * @return  array | object | Closure
     */
    public function createCallableController()
    {
        $ctrl = $this->getController();
        if (is_callable($ctrl)) {
            return $ctrl;
        }

        $action = new $ctrl();
        $method = $this->getMethod();
        if (null === $method) {
            $method = 'execute';
        }
        $call = array($action, $method);
        if (! is_callable($call)) {
            $err = "could not create callable action -($ctrl, $method)";
            throw new LogicException($err);
        }
            
        return $call;
    }

    /**
     * @return  array
     */
    public function getCaptures()
    {
        return $this->captures;
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
    protected function setController($ctrl)
    {
        $this->controller = $ctrl;
    }

    /**
     * @param  string  $key
     * @return  null
     */
    protected function setMethod($name)
    {
        if (! $this->isValidString($name)) {
            $err = "controller method must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->method = $name;
    }


    /**
     * @param   array   $params
     * @return  null
     */
    protected function setCaptures(array $list)
    {
        $this->captures = $list;
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
