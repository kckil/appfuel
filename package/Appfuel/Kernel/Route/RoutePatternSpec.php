<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
    OutOfBoundsException,
    InvalidArgumentException;
/**
 * Value object used to hold the route key, regex pattern, and group for a 
 * given route. It is used by the route manager to process the uri matching it
 * to the correct route.
 */
class RoutePatternSpec implements RoutePatternSpecInterface
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
     * Allow the pattern to change based on the method
     * @var array
     */
    protected $map = array(
        'default' => null,
        'get'     => null,
        'post'    => null,
        'put'     => null,
        'delete'  => null,
    );

    /**
     * Name of the group this pattern will be sorted into. Pattern groups help
     * minimize the number of patterns needed to search through. All patterns
     * that belong to no groups will be given the -(no-group) group
     * @var string
     */
    protected $group = 'no-group';

    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data)
    {
        if (! isset($data['route-key'])) {
            $err = '-(route-key) route key is expected but not given';
            throw new OutOfBoundsException($err);
        }
        $this->setRouteKey($data['route-key']);

        if (isset($data['compiled-pattern'])) {
            $this->setPattern($data['compiled-pattern']);
        }

        if (isset($data['pattern-group'])) {
            $this->setGroup($data['pattern-group']);
        }
    }

    /**
     * @return  string
     */
    public function getRouteKey()
    {
        return $this->key;
    }

    /**
     * @return  string
     */
    public function getPattern($method = null)
    {
        if (null === $method) {
            return $this->getDefaultPattern();
        }

        if (! is_string($method)) {
            $err = "http method must be string";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->map[$method])) {
            if (isset($this->map['default'])) {
                return $this->map['default'];
            }

            return false;
        }


        return $this->map[$method];
    }

    /**
     * @return  string | null when not set
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param   string  $key
     * @return  null
     */
    protected function setRouteKey($key)
    {
        if (! is_string($key)) {
            throw new InvalidArgumentException("route key must be a string");
        }

        $this->key = $key;
    }

    /**
     * @param   string  $pattern
     * @return  null
     */
    protected function setPattern(array $map)
    {
        foreach ($map as $key => $pattern) {
            if (! array_key_exists($key, $this->map)) {
                $keys = explode(',', array_keys($this->map));
                $err = "pattern map key must be one of the following -($keys)";
                throw new DomainException($err);
            }

            if (null !== $pattern && 
                (! is_string($pattern) || empty($pattern))) {
                $err = "regex pattern must be a non empty string";
                throw new DomainException($err);
            }

            $this->map[$key] = $pattern;
        }
    }

    /**
     * @param   string  $group
     * @return  null
     */
    protected function setGroup($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "group must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->group = $name;
    }
}
