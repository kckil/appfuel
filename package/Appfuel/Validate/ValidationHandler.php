<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

use RunTimeException,
    InvalidArgumentException;

/**
 * The facade infront of the coordinator, validators and filters. It is the 
 * public interface appfuel uses to validate any kind of input
 */
class ValidationHandler implements ValidationHandlerInterface
{
    /**
     * Used to handle the movement of data and errors between the 
     * validation subsystems
     * @var Coordinator
     */
    protected $coord = null;

    /**
     * Holds a list of validators based on field name
     * @var array
     */
    protected $validators = array();

    /**
     * @param   CoordinatorInterface
     * @return  Controller
     */
    public function __construct(CoordinatorInterface $coord = null)
    {
        if (null === $coord) {
            $coord = ValidationFactory::createCoordinator();
        }
        $this->setCoordinator($coord);
    }

    /**
     * @return  CoordinatorInterface
     */
    public function getCoordinator()
    {
        return $this->coord;    
    }

    /**
     * @param   CoordinatorInterface
     * @return  ValidationHandler
     */
    public function setCoordinator(CoordinatorInterface $coord)
    {
        $this->coord = $coord;
        return $this;
    }

    /**
     * @param   FieldSpecInterface  $spec
     * @return  ValidationHandler
     */
    public function loadSpec(FieldSpecInterface $spec)
    {
        $key = $spec->getValidator();
        if (! is_string($key) || empty($key)) {
            $key = null;
        }
        $validator = ValidationFactory::createValidator($key);
        $validator->loadSpec($spec);
        $this->addValidator($validator);
        return $this;
    }

    /**
     * @param   ValidatorInterface  $validator
     * @return  ValidationHandler
     */
    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
        return $this;
    }

    /**
     * @return  array
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @return  ValidationHandler
     */
    public function clearValidators()
    {
        $this->validators = array();
        return $this;
    }

    /**
     * @return  bool
     */
    public function isError()
    {
        return $this->getCoordinator()
                    ->isError();
    }

    /**
     * @return  array
     */
    public function getErrorStack()
    {
        return $this->getCoordinator()
                    ->getErrorStack();
    }

    /**
     * @return  ValidationHandler
     */
    public function clearErrors()
    {
        $this->getCoordinator()
             ->clearErrors();

        return $this;
    }

    /**
     * @return  array
     */
    public function getAllClean()
    {
        return $this->getCoordinator()
                    ->getAllClean();
    }

    /**
     * @return  mixed
     */
    public function getClean($field, $default = null)
    {
        return $this->getCoordinator()
                    ->getClean($field, $default);
    }

    /**
     * @return  ValidationHandler
     */
    public function clearClean()
    {
        $this->getCoordinator()
             ->clearClean();

        return $this;
    }

    /**
     * @param   mixed   $raw    data used to validate with filters
     * @return  bool
     */
    public function isSatisfiedBy(array $raw)
    {
        $coord = $this->getCoordinator();
        
        /* 
         * Clear any errors, clean and raw data. this allows filters to be
         * reused across multiple raw sources
         */
        $coord->clearErrors();
        $coord->setSource($raw);
        
        $failed = 0;
        $validators = $this->getValidators();
        foreach ($validators as $validator) {
            if (! $validator->isValid($coord)) {
                $failed++;
            }
        }
        $this->clearValidators();

        if ($failed > 0) {
            return false;
        }

        return true;
    }
}
