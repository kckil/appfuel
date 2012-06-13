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

/**
 * Value object used to hold the route key, regex pattern, and group for a 
 * given route. It is used by the route manager to process the uri matching it
 * to the correct route.
 */
interface RoutePatternInterface
{
	/**
	 * @return string
	 */
	public function getRouteKey();

	/**
	 * @return string
	 */
	public function getRegEx();

	/**
	 * @return	string | null when not set
	 */
	public function getGroup();
}
