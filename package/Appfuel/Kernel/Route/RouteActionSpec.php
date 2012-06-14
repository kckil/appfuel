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
 * Maps the input method (http[get,post,put,delete] or cli)
 * to a concrete MvcAction.
 */
class RouteActionSpec implements RouteActionSpecInterface
{
	/**
	 * Name of the mvc action class. This is not the qual
	 * @var string
	 */
	protected $name = null;

	/**
	 * @var array
	 */
	protected $map = array();

	/**
	 * @var string
	 */
	protected $namespace = array();

	/**
	 * @param	array	$spec
	 * @return	RouteAction
	 */
	public function __construct(array $spec)
	{
		if (! isset($spec['namespace'])) {
			$err = "mvc action namespace is required but not set";
			throw new DomainException($err);
		}
		$this->setNamespace($spec['namespace']);

		if (! isset($spec['action-name']) && ! isset($spec['map'])) {
			$err  = 'the action name or map must be set in order for the ';
			$err .= ' dispatcher to be able to create it';
			throw new DomainException($err);
		}

		if (isset($spec['map'])) {
			$this->setMap($spec['map']);
		}
		else if (isset($spec['action-name'])) {
			$this->setName($spec['action-name']);
		}
		else {
			$err  = 'key -(action-map|action-name) must be non empty string ';
			$err .= 'or an array of method=>actionName mappings';
			throw new DomainException($err);
		}
	}

	/**
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findAction($method = null, $isQualified = true)
	{
		if ($this->isMapEmpty()) {
			$name = $this->getName();
		}
		else {
			$name = $this->getNameInMap($method);
		}

		if (true === $isQualified && ! empty($name)) {
			$name = "{$this->getNamespace()}\\$name";
		}

		return $name;
	}

	/**
	 * @return	string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param	string	$method
	 * @return	string | false
	 */
	protected function getNameInMap($method)
	{
		if (! is_string($method) || ! isset($this->map[$method])) {
			return false;
		}

		return $this->map[$method];
	}

	/**
	 * @return	bool
	 */
	protected function isMapEmpty()
	{
		return empty($this->map);
	}

	/**
	 * @return	array
	 */
	protected function getMap()
	{
		return $this->map;
	}

	/**
	 * @param	array	$map
	 * @return	RouteAction
	 */
	protected function setMap(array $map)
	{
		foreach ($map as $method => $action) {
			if (! is_string($method) || empty($method)) {
				$err = "action map method must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($action) || empty($action)) {
				$err = "action map action must be a non empty string";
				throw new DomainException($err);
			}
		}

		$this->map = $map;
		return $this;
	}

	/**
	 * @return	string
	 */
	protected function getName()
	{	
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	protected function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "action name must be a non empty string";
			throw new DomainException($err);
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	protected function setNamespace($ns)
	{
		if (! is_string($ns)) {
			$err = "mvc action namespace must be a string";
			throw new DomainException($err);
		}

		$this->namespace = $ns;
	}
}
