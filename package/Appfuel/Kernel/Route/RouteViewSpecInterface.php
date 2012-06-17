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

interface RouteViewSpecInterface
{
	/**
	 * @return string
	 */
	public function getFormat();

	/**
	 * @return	bool
	 */
	public function isViewDisabled();

	/**
	 * @return	bool
	 */
	public function isManualView();

	/**
	 * @return	bool
	 */
	public function isViewPackage();

	/**
	 * @return	string
	 */
	public function getViewPackage();
}
