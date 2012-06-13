<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use Exception,
	DomainException,
	InvalidArgumentException,
	Appfuel\Filesystem\FileFinder,
	Appfuel\Filesystem\FileReader,
	Appfuel\ClassLoader\NamespaceParser;

/**
 */
class RouteManager
{
	/**
	 * Flag used to determine if regexes will be used to find the route
	 * @var bool
	 */
	static protected $isPatternMatching = true;

	/**
	 * Flag used to determine if a route can be found via its route key
	 * @var bool
	 */
	static protected $isKeyLookup = true;

	/**
	 * Flag used to determine if the pattern will be searched before looking for
	 * the route key. This is used when both pattern matching and key lookup
	 * are both enabled.
	 * @var bool
	 */
	static protected $isPatternFirst = false;

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
	static protected $groupPatternMap = array();

	/**
	 * @return	bool
	 */
	static public function isPatternMatching()
	{
		return self::$isPatternMatching;
	}

	/**
	 * @return	null
	 */
	static public function enablePatternMatching()
	{
		self::$isPatternMatching = true;
	}

	/**
	 * @return	null
	 */
	static public function disablePatternMatching()
	{
		self::$isPatternMatching = false;
	}

	/**
	 * @return	bool
	 */
	static public function isKeyLookup()
	{
		return self::$isKeyLookup;
	}

	/**
	 * @return	null
	 */
	static public function enableKeyLookup()
	{
		self::$isKeyLookup = true;
	}

	/**
	 * @return	null
	 */
	static public function disableKeyLookup()
	{
		self::$isKeyLookup = false;
	}

	/**
	 * @return	null
	 */
	static public function usePatternMatchingBeforeKeyLookup()
	{
		self::$isPatternFirst = true;
	}

	/**
	 * @return	null
	 */
	static public function useKeyLookupBeforePatternMatching()
	{
		self::$isPatternFirst = false;
	}

	/**
	 * @return	bool
	 */
	static public function isPatternMatchingBeforeKeyLookup()
	{
		return self::$isPatternFirst;
	}

	/**
	 * @return	null
	 */
	static public function clearPatternMap()
	{
		self::$patternMap = array();
	}

	/**
	 * @return	array
	 */
	static public function getPatternMap()
	{
		return self::$patternMap;
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function setPatternMap(array $map)
	{
		self::clearPatternMap();
		self::loadPatternMap($map);
	}

	/**
	 * @param	array	$map
	 * @return	null
	 */
	static public function loadPatternMap(array $map)
	{
		foreach ($map as $pattern) {
			self::addPattern($pattern);
		}
	}

	/**
	 * @param	string	$key
	 * @param	string	$pattern
	 * @return	null
	 */
	static public function addPattern($pattern)
	{	
		if (is_string($pattern)) {
			$pattern = self::createRoutePattern($pattern);
		}
		else if (! $pattern instanceof RoutePatternInterface) {
			$err  = "route pattern must be an array of pattern data or an ";
			$err .= "object that implements Appfuel\Kernel\Mvc\\RoutePattern";
			$err .= "Interface";
			throw new DomainException($err);
		}

		$group	 = $pattern->getGroup();
		$key     = $pattern->getRouteKey();
		self::$patternMap[$group][$key] = $pattern;
	}

	/**
	 * @param	array	$data
	 * @return	RoutePattern
	 */
	static public function createPattern(array $data)
	{
		return new RoutePattern($data);
	}

	/**
	 * @return	null
	 */
	static public function clearRoutes()
	{
		self::$routes = array();
	}

	/**
	 * @return	array
	 */
	static public function getRoutes()
	{
		return self::$routes;
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function setRoutes(array $list)
	{
		self::clearRoutes();
		self::loadRoutes($list);
	}

	/**
	 * @param	array	$list
	 * @return	null
	 */
	static public function loadRoutes(array $list)
	{
		foreach ($list as $route) {
			self::addRoute($route);
		}
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isRoute($key)
	{
		if (! is_string($key) || ! isset(self::$routes[$key])) {
			return false;
		}

		return true;
	}

	/**
	 * @throws	DomainException
	 * @param	array | MvcRouteDetailInterface
	 * @return	null
	 */
	static public function addRoute($route)
	{
		if (is_array($route)) {
			$route = self::createRoute($route);
		}
		else if (! $route instanceof RouteInterface) {
			$err  = "route detail must be an array (detail spec) or an oject ";
			$err .= "that implements -(Appfuel\Kernel\Mvc\\RouteInterface)";
			throw new DomainException($err);
		}

		$key = $route->getKey();
		if (self::isRoute($key)) {
			$err = "can not add route -($key) because it has already exists";
			throw new DomainException($err);
		}

		self::$routes[$key] = $route;

		if (! self::isPatternMatching() || ! $route->isPattern()) {
			return;
		}

		self::addPattern($route->getPattern(), $key, $route->getPatternGroup());
	}

	/**
	 * @param	array	$data
	 * @return	MvcRouteDetailInterface
	 */
	static public function createRoute(array $data)
	{
		if (! isset($data['route-class'])) {
			return new Route($data);
		}
			
		$class = $data['route-class'];
		if (! is_string($class) || empty($class)) {
			$err  = "class declared by -(route-detail-class) must be ";
			$err .= "non empty string";
			throw new DomainException($err);
		}
		
		try {
			$route = new $class($data);
		}
		catch (Exception $e) {
			$err = "could not instantiate route detail -($class)";
			throw new DomainException($err);
		}
	
		if (! $route instanceof RouteInterface) {
			$err  = "route detail -($class) does not implement -(Appfuel";
			$err .= "\Kernel\Mvc\\RouteInterface)";
			throw new DomainException($err);
		}

		return $route;
	}
}
