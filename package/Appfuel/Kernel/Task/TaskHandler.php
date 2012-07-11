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
     * @param   array   $tasks
     * @param   MvcContextInterface $context
     * @return  array
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
     * @param   string  $className
     * @return  StartupTaskInterface | false
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
