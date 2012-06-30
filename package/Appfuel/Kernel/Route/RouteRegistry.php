<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use Exception,
    DomainException,
    InvalidArgumentException,
    Appfuel\ClassLoader\NamespaceParser;

/**
 */
class RouteRegistry
{
    /**
     * List of routes stored as an associative array route key => detail
     * @var array
     */
    static protected $routes = array();

    /**
     * Associative array of regex to route key. Used by the router where
     * any successful match will point to the route key its associated with
     * @var array
     */ 
    static protected $patternMap = array();

    /**
     * Associative array of goup regexes to match top level urls
     * @var array
     */
    static protected $groupMap = array();

    /**
     * @param   string  $cat
     * @param   string  $key
     * @return  object
     */
    static public function getRouteSpec($cat, $key)
    {
        if (! is_string($cat) || ! isset(self::$routes[$cat][$key])) {
            return false;
        }

        return self::$routes[$cat][$key];
    }

    /**
     * @param   string  $key
     * @param   string  $cat
     * @param   object  $spec
     * @return  null
     */
    static public function addRouteSpec($key, $cat, $spec)
    {
        if (! is_string($key) || empty($key)) {
            $err = "route key must be a non empty string";
            throw new DomainException($err);
        }

        if (! is_string($cat) || empty($cat)) {
            $err = "route category must be a non empty string";
            throw new DomainException($err);    
        }

        $strategy = ucfirst($cat);
        $interface = "Route{$strategy}SpecInterface";
        if (! $spec instanceof $interface) {
            $type  = gettype($spec);
            $class = get_class($spec);
            $err   = "route object given is a -($type, $class) and does not ";
            $err  .= "implement -($interface)";
            throw new DomainException($err);
        }

        if (! isset(self::$routes[$cat])) {
            self::$routes[$cat] = array();
        }

        self::$routes[$cat][$key] = $spec;
    }

    /**
     * @return  null
     */
    static public function clearRouteSpecs()
    {
        self::$routes = array();
    }

    /**
     * @return  null
     */
    static public function clearPatternMap()
    {
        self::$patternMap = array();
    }

    /**
     * @return  array
     */
    static public function getPatternMap()
    {
        return self::$patternMap;
    }
    
    /**
     * @param   string  $group
     * @return  array
     */
    public function getPatterns($group = null)
    {
        if (null === $group) {
            $group = 'no-group';
        }

        if (isset(self::$pattern[$group])) {
            return self::$pattern[$group];
        }

        return array();
    }

    /**
     * @param   string  $key
     * @param   string  $pattern
     * @return  null
     */
    static public function addPattern(RoutePatternSpecInterface $pattern)
    {    
        $group = $pattern->getGroup();
        if (null === $group) {
            $group = 'no-group';
        }
        $key = $pattern->getRouteKey();
        self::$patternMap[$group][$key] = $pattern->getRegEx();
    }

    /**
     * @return  array
     */
    public function getGroupMap()
    {
        return self::$groupMap;
    }

    /**
     * @param   array   $groups
     * @return  null
     */
    public function setGroupMap(array $groups)
    {
        foreach($groups as $pattern => $groupName) {
            if (! is_string($pattern) || empty($pattern)) {
                $err = 'group pattern must be a non empty string';
                throw new DomainException($err);
            }
        }

        self::$groupMap = $groups;
    }
}
