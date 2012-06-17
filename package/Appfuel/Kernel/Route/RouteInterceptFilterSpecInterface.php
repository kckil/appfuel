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

interface RouteInterceptFilterSpecInterface
{
	/**
	 * @param	array	$spec
	 * @return	RouteInterceptFilterSpecInterface
	 */
	public function __construct(array $spec);

	/**
	 * @return	bool
	 */
	public function isPreFilteringEnabled();

	/**
	 * @return	array
	 */
	public function getPreFilters();

	/**
	 * @return	bool
	 */
	public function isPreFilters();

	/**
	 * @return	bool
	 */
	public function isExcludedPreFilters();

	/**
	 * @return	array
	 */
	public function getExcludedPreFilters();

	/**
	 * @return	bool
	 */
	public function isPostFilteringEnabled();

	/**
	 * @return	array
	 */
	public function getPostFilters();

	/**
	 * @return	bool
	 */
	public function isPostFilters();

	/**
	 * @return	bool
	 */
	public function isExcludedPostFilters();

	/**
	 * @return	array
	 */
	public function getExcludedPostFilters();
}
