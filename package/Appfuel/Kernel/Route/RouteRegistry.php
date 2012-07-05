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
     * Indicated the very first route resolved. All internal calls will be
     * logged to the current route to form a route call stack.
     * @var MatchedRouteInterface
     */
    protected $currentRoute = null;

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
    static protected $patterns = array();

    /**
     * Associative array of goup regexes to match top level urls
     * @var array
     */
    static protected $groups = array();

    /**
     * @return  MatchedRouteInterface
     */
    public function getCurrentRoute()
    {
        return self::$currentRoute;
    }

    /**
     * @param   MatchedRouteInterface   $route
     * @return  null
     */
    public function setCurrentRoute(MatchedRouteInterface $route)
    {
        self::$currentRoute = $route;
    }

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

        $parts = explode('-', $cat);
        $strategy = '';
        foreach ($parts as $name) {
            $strategy .= ucfirst($name);
        } 
        
        $interface = __NAMESPACE__ . "\\Route{$strategy}SpecInterface";
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
    static public function clearPatterns()
    {
        self::$patterns = array();
    }

    /**
     * @return  array
     */
    static public function getPatternMap()
    {
        return self::$patterns;
    }
    
    /**
     * @param   string  $group
     * @return  array
     */
    static public function getPatterns($group = null)
    {
        if (null === $group) {
            $group = 'no-group';
        }

        if (isset(self::$patterns[$group])) {
            return self::$patterns[$group];
        }

        return array();
    }

    /**
     * @param   RouteUriSpecInterface   $uri 
     * @return  null
     */
    static public function addPattern(RoutePatternSpecInterface $uri)
    {    
        self::$patterns[$uri->getGroup()][] = $uri->getRouteKey();
    }

    /**
     * @return  array
     */
    static public function getGroupMap()
    {
        return self::$groups;
    }

    /**
     * @param   array   $groups
     * @return  null
     */
    static public function setGroupMap(array $groups)
    {
        foreach($groups as $pattern => $groupName) {
            if (! is_string($pattern) || empty($pattern)) {
                $err = 'group pattern must be a non empty string';
                throw new DomainException($err);
            }
        }

        self::$groups = $groups;
    }
}
