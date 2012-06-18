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

interface RouteInputValidationSpecInterface
{
	/**
	 * @param	array	$spec
	 * @return	RouteInputValidationSpec
	 */
	public function __construct(array $spec);

	/**
	 * @return	bool
	 */
	public function isInputValidation();

	/**
	 * @return bool
	 */
	public function isThrowOnFailure();

	/**
	 * @return	scalar
	 */
	public function getErrorCode();

	/**
	 * @return	bool
	 */
	public function isSpecList();

	/**
	 * @return	array
	 */
	public function getSpecList();
}
