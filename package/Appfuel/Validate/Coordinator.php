<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

use InvalidArgumentException,
    Appfuel\Error\ErrorStack,
    Appfuel\Error\ErrorStackInterface;

/**
 * Coordinates the movement of data. This includes raw fields, clean or 
 * filtered fields and all errors.
 */
class Coordinator implements CoordinatorInterface
{
    /**
     * Data source to validate on
     * @var mixed
     */
    protected $source = array();

    /**
     * Hold data that has been considered safe
     * @var array
     */
    protected $clean = array();

    /**
     * Error message used when criteria fails
     * @var ErrorStackInterface
     */
    protected $errors = null;

    /**
     * @param   ErrorStackInterface $stack
     * @return  Coordinator
     */
    public function __construct(ErrorStackInterface $stack = null)
    {
        if (null === $stack) {
            $stack = $this->createErrorStack();
        }
        $this->setErrorStack($stack);
    }

    /**
     * @return array
     */
    public function getAllClean()
    {
        return $this->clean;
    }

    /**
     * @throws  InvalidArgumentException
     * @param   string  $field
     * @param   mixed   $value
     * @return  Coordinator
     */
    public function addClean($field, $value)
    {
        if (! $this->isValidKey($field)) {
            $err = "can not add field to the clean source, invalid key";
            throw new InvalidArgumentException($err);
        }

        $this->clean[$field] = $value;
        return $this;
    }

    /**
     * @param   string  $field
     * @param   mixed   $default
     * @return  mixed
     */
    public function getClean($field, $default = null)
    {
        if (! $this->isValidKey($field) || 
            ! array_key_exists($field, $this->clean)) {
            return $default;
        }

        return $this->clean[$field];
    }

    /**
     * @return  Coordinator
     */
    public function clearClean()
    {
        $this->clean = array();
        return $this;
    }

    /**
     * @return array
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * The raw source must be an array of key=>value or a dictionary object
     * 
     * @param   mixed
     * @return  Validator
     */
    public function setSource(array $source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param   string  $key
     * @return  mixed | special token to indicate not found
     */
    public function getRaw($field)
    {
        if (! $this->isValidKey($field) ||
            ! array_key_exists($field, $this->source)) {
            return $this->getFieldNotFoundToken();
        }

        return $this->source[$field];
    }

    /**
     * @return  Coordinator
     */
    public function clearSource()
    {
        $this->source = array();
        return $this;
    }

    /**
     * @return  string
     */
    public function getFieldNotFoundToken()
    {
        return CoordinatorInterface::FIELD_NOT_FOUND;
    }

    /**
     * @param   string  $field    the field this error is for
     * @param   string  $txt
     * @return  FilterValidator
     */
    public function addError($msg, $code = 500)
    {
        $this->getErrorStack()
             ->addError($msg, $code);
    
        return $this;
    }

    /**
     * @return  bool
     */
    public function isError()
    {
        return $this->getErrorStack()
                    ->isError();
    }

    /**
     * @return  ErrorStackInterface
     */
    public function getErrorStack()
    {
        return $this->errors;
    }

    /**
     * @param   ErrorStackInterface $stack
     * @return  Coordinator
     */
    public function setErrorStack(ErrorStackInterface $stack)
    {
        $this->errors = $stack;
        return $this;
    }

    /**
     * @return  Coordinator
     */
    public function clearErrors()
    {
        $this->getErrorStack()
             ->clear();

        return $this;
    }

    /**
     * This is used when you want to re-use the coordinator for the same fields
     * but a new set of raw input
     *
     * @return  null
     */    
    public function clear()
    {
        $this->clearClean();
        $this->clearSource();
        $this->clearErrors();
    }

    /**
     * @return  ErrorStack
     */
    protected function createErrorStack()
    {
        return new ErrorStack();
    }

    /**
     * @param   mixed   $key
     * @return  bool
     */
    protected function isValidKey($key)
    {
        if (! (is_string($key) || is_numeric($key)) || strlen($key) === 0) {
            return false;
        }

        return true;
    }
}
