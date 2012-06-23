<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException;
/**
 * Specification used to control the automation of input validation by
 * the dispatcher
 */
class RouteInputValidationSpec implements RouteInputValidationSpecInterface
{
    /**
     * Flag used to tell the dispatcher to ignore input validation
     * @var bool
     */
    protected $isInputValidation = true;

    /**
     * Flag used to determine if the dispatcher should throw an exception
     * when a failure is detected by input validation
     * @var bool
     */
    protected $isThrowOnFailure = true;

    /**
     * Error code used by the dispatcher when throwOnFailure is true
     * @var bool
     */
    protected $errorCode = 500;

    /**
     * Error message to be sent to the user on failure. This message will 
     * replace the error stack message given by the validation module
     * @var string
     */
    protected $errorMsg = null;

    /**
     * List of validation specifications used for input validation
     * @var array
     */
    protected $specList = array();

    /**
     * @param   array   $spec
     * @return  RouteInputValidationSpec
     */
    public function __construct(array $spec)
    {
        if (isset($spec['disable-validation']) && 
            true === $spec['disable-validation']) {
            $this->isInputValidation = false;
        }

        if (isset($spec['disable-validation-failures']) && 
            true === $spec['disable-validation-failures']) {
            $this->isThrowOnFailure = false;
        }

        if (array_key_exists('error-code', $spec)) {
            $this->setErrorCode($spec['error-code']);
        }

        if (isset($spec['validation-spec'])) {
            $this->setSpecList($spec['validation-spec']);
        }
    }

    /**
     * @return  bool
     */
    public function isInputValidation()
    {
        return $this->isInputValidation;
    }

    /**
     * @return bool
     */
    public function isThrowOnFailure()
    {
        return $this->isThrowOnFailure;
    }

    /**
     * @return  scalar
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return  bool
     */
    public function isSpecList()
    {
        return ! empty($this->specList);
    }

    /**
     * @return  array
     */
    public function getSpecList()
    {
        return $this->specList;
    }

    /**
     * @param    scalar    $code
     * @return  RouteInputValidationSpec
     */
    protected function setErrorCode($code)
    {
        if (null !== $code && ! is_scalar($code)) {
            $err = 'error code must be a scalar value or null';
            throw new DomainException($err);    
        }

        $this->errorCode = $code;
    }

    /**
     * @param   array    $list
     * @return  RouteInputValidationSpec
     */
    protected function setSpecList(array $list)
    {
        $this->specList = $list;
        return $this;
    }
}
