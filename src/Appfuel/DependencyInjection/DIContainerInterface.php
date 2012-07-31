<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use Appfuel\DataStructure\ArrayDataInterface;

/**
 * The dependency injection container holds a collection of services which
 * are defined in the applications package configuration
 */
interface DIContainerInterface extends ArrayDataInterface
{
    /**
     * @return  int
     */
    public function dependencyCount();

    /**
     * @param   DependencyInterface $dependency
     * @return  DIContainer
     */
    public function addDependency(DependencyInterface $dependency);

    /**
     * @throws  InvalidArgumentException
     *
     * @param   string|DependencyInterface
     * @return  DIContainer
     */
    public function removeDependency($key);

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $key
     * @return  DependencyInterface
     */
    public function getDependency($key);

    /**
     * @throws  InvalidArgumentException
     *
     * @param   string  $key
     * @return  bool
     */
    public function isDependency($key);

    /**
     * @throws  DomainException
     * @throws  InvalidArgumentException
     *
     * @param   string  $key
     * @return  mixed
     */
    public function getService($key);
}
