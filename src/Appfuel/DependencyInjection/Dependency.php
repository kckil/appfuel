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
     * Flag used to detemine if this dependency can be replaced in the 
     * DIContainer
     */
    protected $isUnique = false;

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
    public function __construct($key, $isUnique = false, $service = null)
    {
        if (! is_string($key) || empty($key)) {
            $err = "service key must be a non empty string";
            throw new InvalidArgumentException($err);
        }
        $this->key = $key;

        if (true === $isUnique) {
            $this->isUnique = true;
        }

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
    public function isUniqueService()
    {
        return $this->isUnique;
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
     * @return  Dependency
     */
    public function clearService()
    {
        $this->service = null;
        return $this;
    }
}
