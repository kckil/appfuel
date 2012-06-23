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

        if (isset($spec['prepend-startup-tasks']) && 
            true === $spec['prepend-startup-tasks']) {    
            $this->isPrepend = true;
        }

        if (isset($spec['only-route-startup-tasks']) &&                                 
            true === $spec['only-route-startup-tasks']) {                               
            $this->isIgnoreConfig = true;
        } 

        if (isset($spec['startup-tasks'])) {                              
            $this->setStartupTasks($spec['startup-tasks']);                           
        }                                                                       
                                                                                 
        if (isset($spec['excluded-startup-tasks'])) {                                    
            $this->setExcludedStartupTasks($spec['excluded-startup-tasks']);          
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
