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

interface RouteAccessSpecInterface
{
	/**
	 * @param	array	$spec
	 * @return	RouteAction
	 */
	public function __construct(array $spec);

	/**
	 * @return	bool
	 */
	public function isPublicAccess();

	/**
	 * @return	bool
	 */
	public function isInternalOnlyAccess();

	/**
	 * @return bool
	 */
	public function isAclAccessIgnored();

	/**
	 * @return	bool
	 */
	public function isAclForEachMethod();

	/**
	 * @param	string	$code
	 * @return	bool
	 */
	public function isAccessAllowed($codes, $method = null);
}
