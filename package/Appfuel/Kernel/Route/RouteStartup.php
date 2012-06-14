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
class RouteStartup
{
	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function isIgnoreConfigStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $spec->isIgnoreConfigStartupTasks();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function isPrependStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $spec->isPrependStartupTasks();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function isStartupDisabled($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $spec->isStartupDisabled();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function isStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $spec->isStartupTasks();
	}

	/**
	 * @param	$key
	 * @return	array
	 */
	static public function getStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $this->getStartupTasks();
	}

	/**
	 * @param	string	$key
	 * @return	string | false
	 */
	static public function isExcludedStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $spec->isExcludedStartupTasks();
	}

	/**
	 * @param	$key
	 * @return	array
	 */
	static public function getExcludedStartupTasks($key)
	{
		$spec = self::getSpec($key);
		if (! $spec) {
			return null;
		}

		return $this->getExcludedStartupTasks();
	}

	/**
	 * @param	string	$key
	 * @return	RouteStartupSpecInterface
	 */
	static protected function getSpec($key)
	{
		return RouteRegistry::getRouteObject($key, 'startup');
	}
}
