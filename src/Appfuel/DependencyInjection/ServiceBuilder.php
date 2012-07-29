<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DependencyInjection;

use LogicException,
    DomainException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface;

class ServiceBuilder implements ServiceBuilderInterface
{
    /**
     * Used to Identity the service in the application settings collection
     * @var string
     */
    protected $serviceKey = null;

    /**
     * List of keys used to identify parameters in the 
     * @var array
     */
    protected $keys = array();

    /**
     * @var ArrayDataInterface
     */
    protected $settings = null;

    /**
     * @var string
     */
    protected $error = null;

    /**
     * @return  string
     */
    public function getServiceKey()
    {
        return $this->serviceKey;
    }

    /**
     * @param   string  $key
     * @return  ServiceBuilder
     */
    public function setServiceKey($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "service key must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->serviceKey = $key;
        return $this;
    }

    /**
     * @return  ArrayDataInterface
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param   ArrayDataInterface  $data
     * @return  StartupTask
     */
    public function setSettings(ArrayDataInterface $data)
    {
        if (! $this->isValidSettings($data)) {
            $key = $this->getServiceKey();
            $msg = $this->getError();
            throw new DomainException("settings for -($key) are invalid: $msg");
        }

        $this->settings = $data;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isValidSettings(ArrayDataInterface $data)
    {
        return true;
    }

    /**
     * @return  bool
     */
    public function isError()
    {
        return is_string($this->error) && ! empty($this->error);
    }

    /**
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function build(DIContainerInterface $container)
    {
        return $container;
    }

    /**
     * List of keys to pull out of the registry
     *
     * @return    array
     */
    public function getKeys()
    {
        return $this->keys;
    }

    /**
     * @param   array    $keys
     * @return  ServiceBuilder
     */
    public function setKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (! is_string($key) || empty($key)) {
                $err = "settings key must be a non empty string";
                throw new DomainException($err);
            }
        }

        $this->keys = $keys;
        return $this;
    }

    /**
     * @param   string  $msg
     * @return  ServiceBuilder
     */
    protected function setError($msg)
    {
        if (! is_string($msg) || empty($msg)) {
            $err = "a service builder error must be a non empty string";
            throw InvalidArgumentException($err);
        }

        $this->error = $msg;
        return $this;
    }
}
