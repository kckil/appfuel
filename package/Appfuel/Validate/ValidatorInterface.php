<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

/**
 * All validators must extend from this interface
 */
interface ValidatorInterface
{
    /**
     * @param   CoordinatorInterface    $coord
     * @return  bool
     */
    public function isValid(CoordinatorInterface $coord);
}
