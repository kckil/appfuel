<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use OutOfBoundsException,
    InvalidArgumentException;

/**
 * Value object used to hold the route key, regex pattern, and group for a 
 * given route. It is used by the route manager to process the uri matching it
 * to the correct route.
 */
class RouteSpec implements RouteSpecInterface
{
    /**
     * Route key used to identify the correct route on a successful match
     * @var string
     */
    protected $key = null;

    /**
     * Regular expression used to match agaist the uri string
     * @var string
     */
    protected $pattern = null;

    /**
     * Fully qualified namespace of the action controller class
     * @var string
     */
    protected $controller = null;

    /**
     * @param   array   $data
     * @return  RouteSpec
     */
    public function __construct(array $data)
    {
        if (! isset($data['key'])) {
            $err = '-(key) route key is expected but not given';
            throw new OutOfBoundsException($err);
        }
        $this->setKey($data['key']);

        if (! isset($data['pattern'])) {
            $err = '-(pattern) regex pattern is expected but not given';
            throw new OutOfBoundsException($err);
        }
        $this->setPattern($data['pattern']);
        
        if (! isset($data['controller'])) {
            $err  = '-(controller) the controller class is expected but ';
            $err .= 'not given';
            throw new OutOfBoundsException($err);
        }
        $this->setController($data['controller']);
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
     * @param   string  $key
     * @return  null
     */
    protected function setKey($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = 'route key must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->key = $key;
    }

    /**
     * @param   string  $pattern
     * @return  null
     */
    protected function setPattern($pattern)
    {
        if (! is_string($pattern) || 0 === strlen($pattern)) {
            $err = 'pattern must a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->pattern = $pattern;
    }

    /**
     * @param   string  $class
     * @return  null
     */
    protected function setController($class)
    {
        if (! is_string($class) || empty($class)) {
            $err = "controller class must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->controller = $class;
    }
}
