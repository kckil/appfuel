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

class LoadableDependency 
    extends Dependency implements LoadableDependencyInterface
{
    /**
      * @var ServiceBuilderInterface
     */
    protected $builder = null;

    /**
     * @param   ServiceBuilderInterface $builder
     * @return  Dependency
     */
    public function __construct($key, ServiceBuilderInterface $builder)
    {
        parent::__construct($key);
        $this->builder = $builder;
    }

    /**
     * @return  ServiceBuilderInterface
     */
    public function getServiceBuilder()
    {
        return $this->builder;
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

        $service = $this->build($container);
        $this->setService($service);

        return $service;
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
