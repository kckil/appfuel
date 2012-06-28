<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use Appfuel\Kernel\Mvc\MvcContextInterface;

interface TaskHandlerInterface
{
    /**
     * @return  string
     */
    public function getStartupTasksKey();

    /**
     * @param   string  $key
     * @return  TaskHandler
     */
    public function setStartupTasksKey($key);

    /**
     * @param   MvcContextInterface $context 
     * @return  array
     */
    public function runTasksUsingRegistry(MvcContextInterface $context = null);

	/**
	 * @param	array	$tasks
     * @param   MvcContextInterface $context
	 * @return	array
	 */
	public function runTasks(array $tasks, MvcContextInterface $context = null);

    /**
     * @param   string  $class
     * @param   array   $params 
     * @param   MvcContextInterface $context
     * @return  bool
     */    
    public function runTask($class, 
                            array $params = null,
                            MvcContextInterface $context = null);

	/**
	 * @param	string	$className
	 * @return	StartupTaskInterface | false
	 */
	public function createTask($className);
}
