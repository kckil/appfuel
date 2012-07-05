<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException;

class MatchedRoute implements MatchedRouteInterface
{
    /**
     * Route key found in pattern match
     * @var string
     */
    protected $key = null;

    /**
     * @var string
     */
    protected $originalUri = null;

    /**
     * @var string
     */
    protected $format = null;

    /**
     * The string that matched the route regex
     * @var string
     */
    protected $routeMatch = null;

    /**
     * @var string
     */
    protected $group = 'no-group';

    /**
     * The string that matched the group regex
     * @var string
     */
    protected $groupMatched = null;

    /**
     * lis of values captured by the group and route regexs
     * @var array
     */
    protected $captures = array();

    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data)
    {
        if (! isset($data['original-uri'])) {
            $err = "-(original-uri) original uri is required and not set";
            throw new DomainException($err);
        }
        $this->originalUri = $this->str($data['original-uri'], 'originalUri');
        
        if (! isset($data['route-key'])) {
            $err = "-(route-key) route key must is required by not set";
            throw new DomainException($err);
        }
        $this->key = $this->str($data['route-key'], 'routeKey');

        if (! isset($data['route-match'])) {
            $err = "-(route-match) route match is required but not set";
            throw new DomainException($err);
        }
        $this->routeMatch = $this->str($data['route-match'], 'routeMatch');

        if (isset($data['unmatched']) && ! empty($data['unmatched'])) {
            $this->unmatched = $this->str($data['unmatched'], 'unmatched');
        }

        if (isset($data['group'])) {
            $this->group = $this->str($data['group'], 'group');
            if (isset($data['group-match'])) {
                $gMatch = $data['group-match'];
                $this->groupMatch = $this->str($gMatch, 'groupMatch');
            }
        }

        if (isset($data['format'])) {
            $this->format = $this->str($data['format'], 'format');
        }

        if (isset($data['final-captures']) 
            && is_array($data['final-captures'])) {
            $this->captures = $data['final-captures'];
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
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @return  bool
     */
    public function isFormat()
    {
        $format = $this->format;
        return is_string($format) && ! empty($format);
    }

    /**
     * @return  string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return  string
     */
    public function getGroupMatch()
    {
        return $this->groupMatch;
    }

    /**
     * @return  string
     */
    public function getRouteMatch()
    {
        return $this->routeMatch;
    }

    /**
     * @return  string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return  array
     */
    public function getCaptures()
    {
        return $this->captures;
    }

    /**
     * @throws  DomainException
     * @param   string  $str
     * @return  string  
     */
    protected function str($str, $member) 
    {
         if (! is_string($str) || empty($str)) {
            $err = "-($member) must a none empty string";
            throw new DomainException($err);
        }

        return $str;
    }
}
