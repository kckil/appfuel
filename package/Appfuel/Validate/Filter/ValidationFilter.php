<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate\Filter;

use InvalidArgumentException,
    Appfuel\DataStructure\DictionaryInterface;

/**
 * Create the filter from the name given. In this case the 
 */
class ValidationFilter implements FilterInterface
{
    /**
     * The name this filter was mapped with
     * @var string
     */
    protected $name = null;

    /**
     * Dictionary of options used to control the filter's behavior
     * @var DictionaryInterface
     */
    protected $options = null;

    /**
     * Message used when this filter fails
     * @var string
     */
    protected $error = null;

    /**
     * @var scalar
     */
    protected $errorCode = 500;

    /**
     * Since the default value can be anything we use the failure token as
     * a way to determine when it has been set
     *
     * @var mixed
     */
    protected $default = FilterInterface::DEFAULT_NOT_SET;
    
    /**
     * @param   FilterSpecInterface $spec
     * @return  ValidationFilter
     */
    public function loadSpec(FilterSpecInterface $spec)
    {
        $this->setName($spec->getName());
        $options = $spec->getOptions();
        if ($options instanceof DictionaryInterface) {
            $this->setOptions($options);
        }

        $error = $spec->getError();
        if (! empty($error)) {
            $this->setError($error);
        }

        /* do not need to check because the spec has default value */
        $this->setErrorCode($spec->getErrorCode());
        
        return $this;
    }

    /**
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param   string  $name
     * @return  ValidationFilter
     */
    public function setName($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "filter name must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return  ValidationFilter
     */
    public function clearName()
    {
        $this->name = null;
        return $this;
    }

    /**
     * @return  mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param   mixed   $value
     * @return  ValidationFilter
     */
    public function setDefault($value)
    {
        $this->default = $value;
        return $this;
    }

    /**
     * We use the a unique token which is replace when set, to determine
     * if the default value has be set
     *
     * @return  bool
     */
    public function isDefault()
    {
        return $this->default !== $this->getDefaultNotSetToken();
    }

    /**
     * @return    ValidationFilter
     */
    public function clearDefault()
    {
        $this->default = $this->getDefaultNotSetToken();
        return $this;
    }

    /**
     * @return  string
     */
    public function getDefaultNotSetToken()
    {
        return FilterInterface::DEFAULT_NOT_SET;
    }

    /**
     * @return  DictionaryInterface
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @todo    replace with ArrayDataInterface
     * @param   DictionaryInterface $options
     * @return  ValidationFilter
     */
    public function setOptions(DictionaryInterface $options)
    {
        $this->options = $options;

        $default = $options->get('default', $this->getDefaultNotSetToken());
        $this->setDefault($default);
        return $this;
    }
    
    /**
     * @return  bool
     */
    public function isOptions()
    {
        return $this->options instanceof DictionaryInterface;
    }

    /**
     * @param   string  $name
     * @param   mixed   $default
     * @return  mixed
     */
    public function getOption($name, $default = null)
    {
        if (! $this->isOptions() || ! is_string($name)) {
            return $default;
        }

        return $this->getOptions()
                    ->get($name, $default);
    }

    /**
     * @return  ValidationFilter
     */
    public function clearOptions()
    {
        $this->options = null;
        return $this;
    }

    /**
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param   string  $text
     * @return  ValidationFilter
     */
    public function setError($text)
    {
        if (! is_string($text)) {
            $err = "error text must be a string";
            throw new InvalidArgumentException($err);
        }

        $this->error = $text;
        return $this;
    }

    /**
     * @return  ValidationFilter
     */
    public function clearError()
    {
        $this->error = null;
        return $this;
    }

    /**
     * @return  scalar
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param   scalar  $code
     * @return  ValidationFilter
     */
    public function setErrorCode($code)
    {
        if (! is_scalar($code)) {
            $err = "error code must be a scalar value";
            throw new InvalidArgumentException($err);
        }

        $this->errorCode = $code;
        return $this;
    }

    /**
     * @return  string
     */
    public function getFailureToken()
    {
        return FilterInterface::FAILURE;
    }

    /**
     * @return  bool
     */
    public function isFailure($result)
    {
        return $result === $this->getFailureToken();
    }

    /**
     * @return  mixed
     */
    public function getFailure()
    {
        $isDefault = $this->isDefault();
        return ($isDefault) ? $this->getDefault() : $this->getFailureToken(); 
    }

    /**
     * Can not have a abstract method and a defined interface. The interface 
     * is more important so we forego the abstract class and throw a 
     * LogicException instead
     *
     * @param   $raw    
     * @return  null
     */
    public function filter($raw)
    {
        throw new LogicException("should be extended");
    }

    /**
     * @return  ValidationFilter
     */
    public function clear()
    {
        $this->clearName()
             ->clearOptions()
             ->clearDefault()
             ->clearError();
    }
}
