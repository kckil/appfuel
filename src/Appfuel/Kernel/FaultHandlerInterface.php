<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use Appfuel\Log\Logger,
    Appfuel\Log\LoggerInterface;

/**
 * The fault handler is responsible for handling uncaught exceptions and
 * php errors.
 */
interface FaultHandlerInterface
{
    /**
     * Used to log the error and exceptions
     * @return	LoggerInterface
     */
    public function getLogger();
}
