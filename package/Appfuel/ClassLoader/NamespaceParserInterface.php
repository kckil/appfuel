<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\ClassLoader;

/**
 * Parse a namespace into a file path
 */
interface NamespaceParserInterface
{
    /**
     * Resolve php namespace first otherwise resolve as pear name 
     *
     * @param   string  $class
     * @param   bool    $isExtension    default true
     * @return  string
     */    
    static public function parse($class, $isExtension = true);
}
