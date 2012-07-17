<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */

/**
 * Derived from Symfony\Component\ClassLoader\ClassCollectionLoader
 * @author  orginal author Fabian Potencier <fabien@symfony.com>
 * @author  Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 */
class CodeCacheHandler
{
    /**
     * Map used to determine if a particular cache has been loaded
     * @var array
     */
    protected $loaded = array();

    /**
     * Used to track files seen when collecting class hierarchies
     * @var array
     */
    protected $seen = array();

    /**
     * @param   CodeCacheOptionsInterface   $options
     * @return  bool
     */
    static public function load(CodeCacheOptions $options)
    {

    }

    /**
     * @param   string  $srouce
     * @return  string
     */
    static public function fixNamespaceDeclarations($source)
    {

    }

    /**
     * @param   string  $source
     * @return  string
     */
    static public function stripComments($source)
    {

    }

    static public function writeCache($file, $content)
    {

    }

    /**
     * @param   array   $classes
     * @return  array
     */
    static public function getOrderedClasses(array $classes)
    {
    
    }

    /**
     * @param   ReflectionClass $class
     * @return  array
     */
    static public function getClassHierarchy(ReflectionClass $class)
    {

    }

    /**
     * @param   ReflectionClass $class
     * @return  array
     */
    static public function getTraits(ReflectionClass $class)
    {

    }
}
