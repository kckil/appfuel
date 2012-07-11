<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Error;

/**
 * The error stack handles a collection of errors
 */
interface ErrorStackInterface 
{
    /**
     * @param   ErrorInterface  $error
     * @return  ErrorStackInterface
     */
    public function addErrorItem(ErrorInterface $error);

    /**
     * @param   string  $text    
     * @param   scalar  $code
     * @return  ErrorStackInterface
     */
    public function addError($msg, $code = null);

    /**
     * Alias for current
     *
     * @return  ErrorInterface | false when no error exists
     */
    public function getError();

    /**
     * @return  ErrorInterface | false when no error exists
     */
    public function getLastError();

    /**
     * Clears all errors out of the stack
     * @return  ErrorStackInterface
     */
    public function clear();
}
