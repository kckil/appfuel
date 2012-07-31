<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use DomainException,
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
     * @var Closure
     */
    protected $loader = null;

    /**
     * @param   ServiceBuilderInterface $builder
     * @return  Dependency
     */
    public function __construct($key, 
                                ServiceBuilderInterface $builder, 
                                $isUniqueService = false)
    {
        parent::__construct($key, $isUniqueService);
        $this->builder = $builder;
        $this->loader = $this->createLoader($key, $builder);
    }

    /**
     * @return  ServiceBuilderInterface
     */
    public function getServiceBuilder()
    {
        return $this->builder;
    }

    /**
     * @return  Closure
     */
    public function getLoader()
    {
        return $this->loader;
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

        $loader = $this->getLoader();
        $service = $loader($container);  
        $this->setService($service);

        return $service;
    }

    /**
     * @param   DIContainerInterface $container 
     * @return  mixed
     */
    public function build(DIContainerInterface $container)
    {
        $loader = $this->getLoader();
        return $loader($container);
    }

    /**
     * @param   string  $key
     * @param   ServiceBuilderInterface $builder
     * @return  Closure
     */
    protected function createLoader($key,ServiceBuilderInterface $builder)
    {
        if ($this->isUniqueService()) {
            return function ($container) use ($key, $builder) {
                static $service = null;
                if (null === $service) {
                    $keys = $builder->getSettingsKeys();
                    $builder->setSettings($container->collect($keys, false));
                    $service = $builder->build($container);
                    if (false === $service) {
                        $err  = "static loading failed. build error: -($key, ";
                        $err .= "{$builder->getError()})";
                        throw new DomainException($err);
                    }
                }   

                return $service;
            };
        }

        return function ($container) use ($key, $builder) {
            $keys = $builder->getSettingsKeys();
            $builder->setSettings($container->collect($keys, false));
            $service = $builder->build($container);
            if (false === $service) {
                $err  = "static loading failed. build error: -($key, ";
                $err .= "{$builder->getError()})";
                    throw new DomainException($err);
            }

            return $service;
        };   
    }
}
