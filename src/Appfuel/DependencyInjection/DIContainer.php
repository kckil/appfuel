<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use LogicException,
    DomainException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayData;

/**
 * The dependency injection container holds a collection of services which
 * are defined in the applications package configuration
 */
class DIContainer extends ArrayData implements DIContainerInterface
{
    /**
     * List of Dependency objects required by the system
     * @var array
     */
    protected $depends = array();

    /**
     * @return  int
     */
    public function dependencyCount()
    {
        return count($this->depends);
    }

    /**
     * @param   DependencyInterface $dependency
     * @return  DIContainer
     */
    public function addDependency(DependencyInterface $dependency)
    {
        $this->depends[$dependency->getServiceKey()] = $dependency;
        return $this;
    }

    /**
     * @throws  InvalidArgumentException
     *
     * @param   string|DependencyInterface $key
     * @return  DIContainer
     */
    public function removeDependency($key)
    {
        if ($key instanceof DependencyInterface) {
            $key = $key->getServiceKey();
        }
        else if (! is_string($key) || empty($key)) {
            $err  = "parameter must be a string or an object that implements ";
            $err .= "-(Appfuel\\DependencyInjection\\DependencyInterface)";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->depends[$key])) {
            return $this;
        }

        unset($this->depends[$key]);
        return $this;
    }

    /**
     * @throws  LogicException
     *
     * @param   string  $key
     * @return  DependencyInterface
     */
    public function getDependency($key)
    {
        if (! $this->isDependency($key)) {
            return false;
        }

        return $this->depends[$key];
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isDependency($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "service key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        if (! isset($this->depends[$key])) {
            return false;
        }

        return true;
    }

    /**
     * @param   string  $key
     * @return  mixed
     */
    public function getService($key)
    {
        $dependency = $this->getDependency($key);
        if (false === $dependency) {
            $err = "a dependency has not been added for this service -($key)";
            throw new LogicException($err);
        }

        if (! $dependency->isServiceAvailable()) {
            if (! $this->isDependencyLoadable($dependency)) {
                $err  = "service -($key) is not available and was not added ";
                $err .= "as a loadable dependency";
                throw new LogicException($err);
            }
            $dependency->loadService($this);
        }

        return $dependency->getService();
    }

    /**
     * @param   mixed   $dependency
     * @return  bool
     */
    public function isDependencyLoadable(DependencyInterface $dependency)
    {
        return $dependency instanceof LoadableDependencyInterface;
    }
}
