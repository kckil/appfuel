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
     * Used to dermine that the $obj is an instance of the service that this
     * builder builds.
     *
     * @param   mixed   $obj
     * @return  bool
     */
    public function isService($obj)
    {
        throw new LogicException("concrete class must implement this method");
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
     * @return  ServiceBuilder
     */
    public function setSettings(ArrayDataInterface $data)
    {
        if (! $this->isValidSettings($data)) {
            $msg = $this->getError();
            throw new DomainException("dependency settings are invalid: $msg");
        }

        $this->settings = $data;
        return $this;
    }

    /**
     * @return  ServiceBuilder
     */
    public function clearSettings()
    {
        $this->settings = null;
        return $this;
    }

    /**
     * @param   array | ArrayDataInterface
     * @return  ServiceBuilder
     */
    public function overrideSettings($data)
    {
        if ($data instanceof ArrayDataInterface) {
            $data = $data->getAll();
        }
        else if (! is_array($data)) {
            $err  = "settings must be an array or an object that implements ";
            $err .= "-(Appfuel\\DataStructure\\ArrayDataInterface)";
            throw new DomainException($err);
        }

        $settings = $this->getSettings();
        if (null === $settings) {
            $this->setSettings($this->createArrayData($data));
            return $this;
        }

        $settingData = $settings->getAll();
        $result = array_replace_recursive($settingsData, $data);
        $this->setSettings($this->createArrayData($result));
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
     * @param   string  $msg
     * @return  ServiceBuilder
     */
    public function setError($msg)
    {
        if (! is_string($msg) || empty($msg)) {
            $err = "a service builder error must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->error = $msg;
        return $this;
    }

    /**
     * @return  ServiceBuilder
     */
    public function clearError()
    {
        $this->error = null;
        return $this;
    }

    /**
     * The reason I did not make this an abstract class has to do with the 
     * fact that I can not have an abstract method and a declared interface
     * method with the same name. Having a complete interface is more important
     * then php language details.
     *
     * @param   array   $data 
     * @param   MvcContextInterface $context
     * @return  bool
     */
    public function build(DIContainerInterface $container)
    {
        throw new LogicException('concrete class must extend this method');
    }

    /**
     * List of keys to pull out of the registry
     *
     * @return    array
     */
    public function getSettingsKeys()
    {
        return $this->keys;
    }

    /**
     * @param   array    $keys
     * @return  ServiceBuilder
     */
    public function setSettingsKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (! is_string($key) || empty($key)) {
                $err = "settings key must be a non empty string";
                throw new OutOfBoundsException($err);
            }
        }

        $this->keys = $keys;
        return $this;
    }
}
