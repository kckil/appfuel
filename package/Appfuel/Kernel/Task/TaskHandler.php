<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
	Appfuel\App\AppRegistry,
	Appfuel\Kernel\Mvc\MvcContextInterface;

class TaskHandler implements TaskHandlerInterface
{
    /**
     * Key used in the AppRegistry to list startup tasks that will be run
     * globally for all routes.
     * @var string
     */
    protected $registryKey = 'startup-tasks';

    /**
     * @return  string
     */
    public function getStartupTasksKey()
    {
        return $this->registryKey;
    }

    /**
     * @param   string  $key
     * @return  TaskHandler
     */
    public function setStartupTasksKey($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "Registry key must be a non empty string";
            throw new DomainException($err);
        }

        $this->registryKey = $key;
        return $this;
    }

    /**
     * @param   MvcContextInterface $context 
     * @return  array
     */
    public function runTasksUsingRegistry(MvcContextInterface $context = null)
    {
        $tasks = AppRegistry::get($this->getStartupTasksKey(), array());
        if (! is_array($tasks)) {
            $type = gettype($tasks);
            $err  = "Startup tasks assigned into the AppRegistry must be an ";
            $err .= "array -($type) was given";
            throw new DomainException($err);
        }

        return $this->runTasks($tasks, $context);
    }

	/**
	 * @param	array	$tasks
     * @param   MvcContextInterface $context
	 * @return	array
	 */
	public function runTasks(array $tasks, MvcContextInterface $context = null)
	{	
        $report = array();
		foreach ($tasks as $className) {
            $result = $this->runTask($className, null, $context);
            $report[$className] = $result;
		}

        return $report;
	}

    /**
     * @param   string  $class
     * @param   array   $params 
     * @param   MvcContextInterface $context
     * @return  bool
     */    
    public function runTask($class, 
                            array $params = null,
                            MvcContextInterface $context = null)
    {
        $task = $this->createTask($class);
        if (null !== $params) {
            $task->setParamData($params);
        }
        else {
            $keys   = $task->getRegistryKeys();
            $params = AppRegistry::collect($keys, false);
            $task->setParamData($params);
        }

        if (null !== $context) {
            $task->setContext($context);
        }

        return $task->execute();
    }

	/**
	 * @param	string	$className
	 * @return	StartupTaskInterface | false
	 */
	public function createTask($className)
	{
		if (! is_string($className) || empty($className)) {
			$err = "startup task class name must be a non empty string";
			throw new InvalidArgumentException($err);
		}

		$task = new $className();
		if (! $task instanceof StartupTaskInterface) {
			$ns   = __NAMESPACE__;
			$err  = "-($className) must implement $ns\StartupTaskInterface";
			throw new RunTimeException($err);
		}

		return $task;
	}
}
