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

use InvalidArgumentException;

/**
 * Controls view settings like disabling the view or telling the framework
 * you will handle the view manually, what the default format for the view is
 * and in the case of html pages what what does this view use.
 */
class RouteViewSpec implements RouteViewSpecInterface
{
	/**
	 * Used to determine what view format will be used with this route
	 * @var string
	 */
	protected $format = 'html';

	/**
	 * @var	bool
	 */
	protected $isViewDisabled = false;

	/**
	 * Determines if the framework needs to compose the view from the view data
	 * @var bool
	 */
	protected $isManualView = false;

	/**
	 * Name of the view package which represents the view for this route.
	 * View packages are generally html pages
	 * @var string
	 */
	protected $viewPkg = null;

	/**
	 * @param	array	$spec
	 * @return	ActionViewSpec
	 */
	public function __construct(array $spec)
	{
		if (isset($spec['is-view']) && false === $spec['is-view']) {
			$this->isViewDisabled = false;
		}

		if (isset($spec['is-manual-view']) && 
			true === $spec['is-manual-view']) {
			$this->isManualView = true;
		}

		if (isset($spec['view-pkg'])) {
			$this->setViewPackage($spec['view-pkg']);
		}

		if (isset($spec['default-format'])) {
			$this->setFormat($spec['default-format']);
		}
	}

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @return	bool
	 */
	public function isViewDisabled()
	{
		return $this->isViewDisabled;
	}

	/**
	 * @return	bool
	 */
	public function isManualView()
	{
		return $this->isManualView;
	}

	/**
	 * @return	bool
	 */
	public function isViewPackage()
	{
		return is_string($this->viewPkg);
	}

	/**
	 * @return	string
	 */
	public function getViewPackage()
	{
		return $this->viewPkg;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setViewPackage($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "package name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$this->viewPkg = $name;
	}

	/**
	 * @param	string	$name
	 * @return	null
	 */
	protected function setFormat($name)
	{
		if (! is_string($name)) {
			$err = 'route format must be a string';
			throw new InvalidArgumentException($err);
		}

		$this->format = $name;
	}
}
