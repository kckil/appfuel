<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

interface RouteInputValidationSpecInterface
{
    /**
     * @param   array    $spec
     * @return  RouteInputValidationSpec
     */
    public function __construct(array $spec);

    /**
     * @return  bool
     */
    public function isInputValidation();

    /**
     * @return  bool
     */
    public function isThrowOnFailure();

    /**
     * @return  scalar
     */
    public function getErrorCode();

    /**
     * @return  bool
     */
    public function isSpecList();

    /**
     * @return  array
     */
    public function getSpecList();
}
