<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
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
     * @param   array   $spec
     * @return  RouteStartup
     */
    public function __construct(array $spec)
    {
        if (isset($spec['disable-startup']) && 
            true === $spec['disable-startup']) {      
            $this->isStartupDisabled = true;            
        }

        if (isset($spec['is-prepended']) && true === $spec['is-prepended']) {    
            $this->isPrepend = true;
        }

        if (isset($spec['is-config-ignored']) &&                                 
            true === $spec['is-config-ignored']) {                               
            $this->isIgnoreConfig = true;
        } 

        if (isset($spec['tasks'])) {                                             
            $this->setStartupTasks($spec['tasks']);                           
        }                                                                       
                                                                                 
        if (isset($spec['excluded-tasks'])) {                                    
            $this->setExcludedStartupTasks($spec['excluded-tasks']);          
        }  
    }

    /**
     * @return    bool
     */
    public function isPrependStartupTasks()
    {
        return $this->isPrepend;
    }

    /**
     * @return    bool
     */
    public function isIgnoreConfigStartupTasks()
    {
        return $this->isIgnoreConfig;
    }

    /**
     * @return    bool
     */
    public function isStartupDisabled()
    {
        return $this->isStartupDisabled;
    }

    /**
     * @return    bool
     */
    public function isStartupTasks()
    {
        return ! empty($this->tasks);
    }

    /**
     * @return    array
     */
    public function getStartupTasks()
    {
        return $this->tasks;
    }

    /**
     * @return    bool
     */
    public function isExcludedStartupTasks()
    {
        return ! empty($this->excludedTasks);
    }

    /**
     * @return    array
     */
    public function getExcludedStartupTasks()
    {
        return $this->excludedTasks;
    }

    /**
     * @param   array   $list
     * @return  null
     */
    protected function setStartupTasks(array $list)
    {
        if (! $this->isValidTaskList($list)) {
            $err = "startup tasks must be non empty strings";
            throw new DomainException($err);
        }

        $this->tasks = $list;
    }

    /**
     * @param   array   $list
     * @return  null
     */
    protected function setExcludedStartupTasks(array $list)
    {
        if (! $this->isValidTaskList($list)) {
            $err = "startup tasks must be non empty strings";
            throw new DomainException($err);
        }

        $this->excludedTasks = $list;
        return $this;
    }

    /**
     * @param   array   $list
     * @return  bool
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
