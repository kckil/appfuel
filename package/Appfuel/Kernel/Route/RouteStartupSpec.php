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
 * Controls which tasks are applied during startup
 */
class RouteStartupSpec implements RouteStartupSpecInterface
{
	/**
	 * The framework should prepend these tasks to the startup task list
	 * @var bool
	 */
	protected $isPrepend = false;

	/**
	 * The framework should ignore any startup tasks defined in the config
	 * @var bool
	 */
	protected $isIgnoreConfig = false;

	/**
	 * Determines is startup tasks should be run
	 * @var bool
	 */
	protected $isStartupDisabled = false;

	/**
	 * List of task class names which are used during application startup
	 * @var array
	 */
	protected $tasks = array();

	/**
	 * List of tasks defined in the config that should be exclude from the
	 * final task list
	 * @var array
	 */
	protected $excludedTasks = array();

	/**
	 * @param	array	$spec
	 * @return	RouteStartup
	 */
	public function __construct(array $spec)
	{
        if (isset($spec['is-disabled']) && true === $spec['is-disabled']) {      
            $this->disableStartup();                                          
        }

        if (isset($spec['is-prepended']) && true === $spec['is-prepended']) {    
            $this->prependStartupTasks();                                     
        }

        if (isset($spec['is-config-ignored']) &&                                 
            true === $spec['is-config-ignored']) {                               
            $this->ignoreConfigStartupTasks();                                
        } 

        if (isset($spec['tasks'])) {                                             
            $this->setStartupTasks($spec['tasks']);                           
        }                                                                       
                                                                                 
        if (isset($spec['excluded-tasks'])) {                                    
            $this->setExcludedStartupTasks($spec['excluded-tasks']);          
        }  
	}

	/**
	 * @return	bool
	 */
	public function isPrependStartupTasks()
	{
		return $this->isPrepend;
	}

	/**
	 * @return	bool
	 */
	public function isIgnoreConfigStartupTasks()
	{
		return $this->isIgnoreConfig;
	}

	/**
	 * @return	bool
	 */
	public function isStartupDisabled()
	{
		return $this->isStartupDisabled;
	}

	/**
	 * @return	bool
	 */
	public function isStartupTasks()
	{
		return ! empty($this->tasks);
	}

	/**
	 * @return	array
	 */
	public function getStartupTasks()
	{
		return $this->tasks;
	}

	/**
	 * @return	bool
	 */
	public function isExcludedStartupTasks()
	{
		return ! empty($this->excludedTasks);
	}

	/**
	 * @return	array
	 */
	public function getExcludedStartupTasks()
	{
		return $this->excludedTasks;
	}

	/**
	 * @return	null
	 */
	protected function prependStartupTasks()
	{
		$this->isPrepend = true;
	}

	/**	
	 * @return	null
	 */
	protected function appendStartupTasks()
	{
		$this->isPrepend = false;
	}

	/**
	 * @return	null
	 */
	protected function ignoreConfigStartupTasks()
	{
		$this->isIgnoreConfig = true;
	}

	/**
	 * @return	null
	 */
	protected function useConfigStartupTasks()
	{
		$this->isIgnoreConfig = false;
	}

	/**
	 * @return	RouteStartup
	 */
	public function enableStartup()
	{
		$this->isStartupDisabled = false;
		return $this;
	}

	/**
	 * @return	RouteStartup
	 */
	public function disableStartup()
	{
		$this->isStartupDisabled = true;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setStartupTasks(array $list)
	{
		if (! $this->isValidTaskList($list)) {
			$err = "startup tasks must be non empty strings";
			throw new DomainException($err);
		}

		$this->tasks = $list;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	RouteStartup
	 */
	public function setExcludedStartupTasks(array $list)
	{
		if (! $this->isValidTaskList($list)) {
			$err = "startup tasks must be non empty strings";
			throw new DomainException($err);
		}

		$this->excludedTasks = $list;
		return $this;
	}

	/**
	 * @param	array	$list
	 * @return	bool
	 */
	protected function isValidTaskLIst(array $list)
	{
		foreach ($list as $task) {
			if (! is_string($task) || empty($task)) {
				return false;
			}
		}

		return true;
	}
}
