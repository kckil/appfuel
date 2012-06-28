<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use LogicException,
    DomainException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

class StartupTask implements StartupTaskInterface
{
    /**
     * List of key used in the AppRegistry to hold data
     * @var array
     */
    protected $keys = array();

    /**
     * @param   ArrayData
     */
    protected $data = null;

    /**
     * @param   array   $keys
     * @return  StartupTask
     */
    public function __construct($data = null)
    {
        if (null === $data) {
            $data = $this->createArrayData(array());
        }
            
        $this->setParamData($data);
    }
    
    /**
     * @return  ArrayDataInterface
     */
    public function getParamData()
    {
        return $this->data;
    }

    /**
     * @param   array | ArrayDataInterface $data
     * @return  StartupTask
     */
    public function setParamData($data)
    {
        if (is_array($data)) {
            $data = $this->createArrayData($data);
        }
        else if (! $data instanceof ArrayDataInterface) {
            $err  = "parameter data must be an array or an object that ";
            $err .= "implments -(Appfuel\DataStructure\ArrayDataInterface)";
            throw new DomainException($err);
        }

        $this->data = $data;
        return $this;
    }

    /**
     * @return  MvcContextInterface
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param   MvcContextInterface $context
     * @return  StartupTask
     */
    public function setContext(MvcContextInterface $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isContext()
    {
        return $this->context instanceof MvcContextInterface;
    }

    /**
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function executeTask(array $data = null,
                                MvcContextInterface $context = null)
    {
        if (null !== $data) {
            $this->setParamData($data);
        }
        
        if (null !== $context) {
            $this->setContext($context);
        }

        return $this->execute();
    }

    /**
     * List of keys to pull out of the registry
     *
     * @return    null|string|array
     */
    public function getRegistryKeys()
    {
        return $this->keys;
    }

    /**
     * This class is ment to be extended. The interface was more important than
     * the abstract class
     *
     * @return  bool
     */
    public function execute()
    {
        throw new LogicException("Please implement this method!");
    }

    /**
     * @param   array    $keys
     * @return  StartupTask
     */
    public function setRegistryKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (! is_string($key) || empty($key)) {
                $err = "registry key must be a non empty string";
                throw new DomainException($err);
            }
        }

        $this->keys = $keys;
        return $this;
    }

    /**
     * @param   array   $data
     * @return  ArrayData
     */
    protected function createArrayData(array $data = null)
    {
        return new ArrayData($data);
    }
}
