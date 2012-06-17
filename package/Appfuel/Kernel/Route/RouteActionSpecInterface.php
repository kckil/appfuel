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

interface RouteActionSpecInterface
{
	/**
	 * @param	array	$spec
	 * @return	RouteAction
	 */
	public function __construct(array $spec);

	/**
	 * @param	string	$method 
	 * @param	bool	$isQualified 
	 * @return	string | false
	 */
	public function findAction($method = null, $isQualified = true);

	/**
	 * @return	string
	 */
	public function getNamespace();

	/**
	 * @param	string	$method
	 * @return	MvcActionInterface
	 */
	public function createAction($method = null);
}
