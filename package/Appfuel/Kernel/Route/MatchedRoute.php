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
     * @var string
     */
    protected $type = 'pattern';

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
        if (! isset($data['route-key'])) {
            $err = "-(route-key) route key must is required by not set";
            throw new DomainException($err);
        }
        $this->key = $this->str($data['route-key'], 'routeKey');

        /*
         * needs to be first because key matches return early but still require
         * the format property
         */
        if (isset($data['format'])) {
            $this->format = $this->str($data['format'], 'format');
        }

        if (isset($data['final-captures']) 
            && is_array($data['final-captures'])) {
            $this->captures = $data['final-captures'];
        }

        /*
         * this route was matched soley by its route key
         */
        if (isset($data['type']) && 'key' === $data['type']) {
            $this->type = 'key';
            return;
        }

        if (! isset($data['original-uri'])) {
            $err = "-(original-uri) original uri is required and not set";
            throw new DomainException($err);
        }
        $uri = $data['original-uri'];
        $this->originalUri = $this->str($uri, 'originalUri', true);
        
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
    }

    /**
     * @return  string
     */
    public function getMatchType()
    {
        return $this->type;
    }

    /**
     * @return  bool
     */
    public function isPatternMatch()
    {
        return 'pattern' === $this->type;
    }

    /**
     * @return  bool
     */
    public function isKeyMatch()
    {
        return 'key' === $this->type;
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
    protected function str($str, $member, $isEmpty = false) 
    {
         if (! is_string($str)) {
            $err = "-($member) must be a string";
            throw new DomainException($err);
        }

        if (false === $isEmpty && empty($str)) {
            $err = "-($member) must not be an empty string";
        }

        return $str;
    }
}
