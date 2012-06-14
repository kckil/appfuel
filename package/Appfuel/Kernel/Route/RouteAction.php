<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Route;

use DomainException;

/**
 * The route action will find the route spec out of the route registry and 
 * provide access to its interface. This is mainly used by the dispatching 
 * system.
 */
class RouteAction
{
	/**
	 * @param	string	$key
	 * @param	string	$method 
	 * @return	string | false
	 */
	static public function findAction($key, $method = null, $isQualfied = true)
	{
		$actionSpec = RouteRegistry::getRouteObject($key, 'action');
		if (! $action) {
			return false;
		}

		return $actionSpec->findAction($method, $isQualified);
	}
}
