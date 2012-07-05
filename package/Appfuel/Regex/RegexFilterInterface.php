<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Regex;

/**
 * Ensure a raw regex is safely escaped for use with preg_* family of functions
 */
interface RegExFilterInterface
{
    public function filter($raw, $modifiers = "");

    /**
     * @throws  InvalidArgumentException
     * @param   string  $raw        the raw regex 
     * @param   string  $modifiers  chars used to modify the regex
     * @return  string  
     */  
    public function convert($raw, $modifiers = "");

    /**
     * @param   string  $raw
     * @return  bool
     */
    public function validate($regex);

    /**
     * @return  bool
     */
    public function isError();

    /**
     * @return  string
     */
    public function getError();

    /**
     * @return  RegExFilterInterface
     */
    public function clearError();
}
