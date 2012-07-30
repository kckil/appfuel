<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use LogicException,
    DomainException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface;

class Dependency implements DependencyInterface
{
    /**
     * Key used to find this service in the container
     * @var string  
     */
    protected $key = null;

    /**
     * @var mixed
     */
    protected $service = null;

    /**
      * @var ServiceBuilderInterface
     */
    protected $builder = null;

    /**
     * @param   ServiceBuilderInterface $builder
     * @return  Dependency
     */
    public function __construct($key, $service = null)
    {
        if (! is_string($key) || empty($key)) {
            $err = "service key must be a non empty string";
            throw new InvalidArgumentException($err);
        }
        $this->key = $key;

        if (null !== $service) {
            $this->setService($service);
        }    
    }

    /**
     * @return  string
     */
    public function getServiceKey()
    {
        return $this->key;
    }

    /**
     * @return  bool
     */
    public function isServiceAvailable()
    {
        return $this->service !== null;
    }

    /**
     * @return  mixed
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param   mixed   $service
     * @return  Dependency
     */
    public function setService($service)
    {
        if (! $this->isValidService($service)) {
            $key = $this->getServiceKey();
            $err = "invalid service -($key): could not set";
            throw new DomainException($err);
        }

        $this->service = $service;
        return $this;
    }

    /**
     * @param   mixed   $service
     * @return  bool
     */
    public function isValidService($service)
    {
        return true;
    }

    /**
     * @param   DIContainerInterface    $container
     * @return  mixed
     */
    public function loadService(DIContainerInterface $container)
    {
        if ($this->isServiceAvailable()) {
            return $this->getService();
        }

        $builder = $this->getServiceBuilder();
        $service = $builder->build($container);
        $this->setService($service);


        return $service;
    }

    /**
     * @return  Dependency
     */
    public function clearService()
    {
        $this->service = null;
        return $this;
    }

    /**
     * @return  ServiceBuilderInterface
     */
    public function getServiceBuilder()
    {
        return $this->builder;
    }

    /**
     *
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function build(DIContainerInterface $container)
    {
        $builder = $this->getServiceBuilder();
        $service = $builder->build($container);
        if (false === $service) {
            $key = $this->getServiceKey();
            $msg = $builder->getError();
            throw new DomainException("failed to build -($key, $msg)");
        }
        
        return $service;
    }
}
