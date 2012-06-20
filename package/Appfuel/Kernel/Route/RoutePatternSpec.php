<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
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
     * @var bool
     */
    protected $key = false;

    /**
     * Regular expression used to match agaist the uri string
     * @var string
     */
    protected $pattern = null;

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
        if ($data === array_values($data)) {
            $pattern = current($data);
            $key     = next($data);
            $group   = next($data);
            if (! $group) {
                $group = null;
            }
        }
        else {
            $key     = null;
            $pattern = null;
            $group   = null;
            if (isset($data['route-key'])) {
                $key = $data['route-key'];
            }
        
            if (isset($data['pattern'])) {
                $pattern = $data['pattern'];
            }

            if (isset($data['group'])) {
                $group = $data['group'];
            }
        }

        $this->setRouteKey($key);
        $this->setRegEx($pattern);

        if (null !== $group) {
            $this->setGroup($group);
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
    public function getRegEx()
    {
        return $this->pattern;
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
    protected function setRegEx($pattern)
    {
        if (! is_string($pattern)) {
            throw new InvalidArgumentException("pattern must be a string");
        }

        $this->pattern = $pattern;
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
