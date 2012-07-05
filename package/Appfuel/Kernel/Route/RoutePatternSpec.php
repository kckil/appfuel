<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use OutOfBoundsException,
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
    protected $patternMap = array(
        'get'    => null,
        'post'   => null,
        'put'    => null,
        'delete' => null,
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

        if (isset($data['pattern'])) {
            $this->setPattern($data['pattern']);
        }

        if (isset($data['pattern-map'])) {
            $this->setPatternMap($data['pattern-map']);
        }

        if (! isset($data['pattern']) && ! isset($data['pattern-map'])) {
            $err  = "either -(pattern) or -(pattern-map) must be defined to ";
            $err .= "inorder to complete the url specification";
            throw new OutofBoundsException($err);
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
        if (null === $method || 
            ! is_string($method) ||
            ! isset($this->patternMap[$method])) {
            return $this->pattern;
        }

        return $this->patternMap[$method];
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
    protected function setPattern($pattern)
    {
        $this->pattern = $this->validatePattern($pattern);
    }

    /**
     * @param   array   $map
     * @return  null
     */
    protected function setPatternMap(array $map)
    {
        foreach ($map as $method => $pattern) {
            $this->patternMap[$method] = $this->validatePattern($pattern); 
        }
    }

    protected function validatePattern($pattern)
    {
        if (is_string($pattern)) {
            return $pattern;
        }

        if (! is_array($pattern)) {
            $err = "pattern must be a string or an array";
            throw new InvalidArgumentException($err);
        }

        if (! isset($pattern[0])) {
            $err = "when pattern is an array it must at least 1 item";
            throw new DomainException($err);
        }

        if (! is_string($pattern[0])) {
            $err = 'pattern must be be a string';
            throw new DomainException($err);
        }

        if (isset($pattern[1]) && ! is_string($pattern[1])) {
            $err = 'pattern modifier must be a string';
            throw new DomainException($err);
        }

        return $pattern;
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
