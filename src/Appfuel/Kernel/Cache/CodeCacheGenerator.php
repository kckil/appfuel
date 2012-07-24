<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Cache;

use DomainException,
    ReflectionClass,
    ReflectionException,
    InvalidArgumentException,
    Appfuel\Filesystem\FileHandlerInterface;

/**
 * Derived from Symfony\Component\ClassLoader\ClassCollectionLoader
 * @author  orginal author Fabian Potencier <fabien@symfony.com>
 * @author  refactored by Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 */
class CodeCacheGenerator
{
    /**
     * Used to track files seen when collecting class hierarchies
     * @var array
     */
    protected static $seen = array();

    /**
     * @param   array   $classes
     * @param   array   $excluded
     * @param   FileHandlerInterface    $fmanager
     * @return  array
     */
    public static function generate(array $classes, 
                                    array $excluded, 
                                    FileHandlerInterface $fHandler)
    {
        $files = array();
        $content = '';

        $patterns = array('/^\s*<\?php/', '/\?>\s*$/');
        foreach (self::getOrderedClasses($classes) as $class) {
            if (in_array($class->getName(), $excluded)) {
                continue;
            }

            $filename = $class->getFileName();
            $files[] = $filename;

            $c = preg_replace($patterns, '', $fHandler->read($filename));
            
            /* add namespace declaration for global code */
            if (! $class->inNamespace()) {
                $c = "\nnamespace\n{\n" . self::stripComments($c) ."\n}\n";
            }
            else {
                $c = self::fixNamespaceDeclarations('<?php ' . $c);
                $c = preg_replace($patterns[0], '', $c);
            }

            $content .= $c;
        }

        return $content;
    }

    /**
     * Adds brackets around each namespace if it's not already the case.
     * (Derived from Symfony\Component\ClassLoader\ClassCollectionLoader)
     * 
     * @author  Fabien Potencier <fabien@symfony.com>
     * @param   string $source Namespace string
     * @return  string
     */
    static public function fixNamespaceDeclarations($source)
    {
        if (! function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        $inNamespace = false;
        $tokens = token_get_all($source);
        
        for ($i = 0, $max = count($tokens); $i < $max; $i++) {
            $token = $tokens[$i];
            if (is_string($token)) {
                $output .= $token;
            }
            else if (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                // strip comments
                continue;
            }
            else if (T_NAMESPACE === $token[0]) {
                if ($inNamespace) {
                    $output .= "}\n";
                }
                $output .= $token[1];

                while (($t = $tokens[++$i]) &&
                       is_array($t) &&
                       in_array($t[0], array(T_WHITESPACE, T_NS_SEPARATOR, T_STRING))
                     ) {
                    $output .= $t[1];
                }
                if (is_string($t) && '{' === $t) {
                    $inNamespace = false;
                    --$i;
                } 
                else {
                    $output = rtrim($output);
                    $output .= "\n{";
                    $inNamespace = true;
                }
                
            }
            else {
                $output .= $token[1];
            }
        }

        if ($inNamespace) {
            $output .= "}\n";
        }

        return $output;
    }

    /**
     * Removes comments from a PHP source string.
     *
     * We don't use the PHP php_strip_whitespace() function
     * as we want the content to be readable and well-formatted.
     *
     * @param   string $source A PHP string
     * @return  string
     */
    public static function stripComments($source)
    {
        if (! function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            }
            else if (! in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= $token[1];
            }
        }

        $output = preg_replace(array('/\s+$/Sm', '/\n+/S'), "\n", $output);

        return $output;
    }

    /**
     * Gets an ordered array of passed classes including all their dependencies.
     * Returns An array of sorted \ReflectionClass instances (dependencies
     * added if needed)
     * 
     * @throws  InvalidArgumentException
     * @param   array $classes
     * @return  array 
     */
    public static function getOrderedClasses(array $classes)
    {
        $map = array();
        self::$seen = array();
        foreach ($classes as $class) {
            try {
                $reflectionClass = new ReflectionClass($class);
            }
            catch (ReflectionException $e) {
                throw new DomainException("Unable to load class -($class)");
            }

            $map = array_merge($map, self::getClassHierarchy($reflectionClass));
        }

        return $map;        
    }

    /**
     * @param   ReflectionClass $class
     * @return  array
     */
    public static function getClassHierarchy(ReflectionClass $class)
    {
        $name = $class->getName();
        if (isset(self::$seen[$name])) {
            return array();
        }

        self::$seen[$name] = true;
        $classes = array($class);
        $parent = $class;
        while (($parent = $parent->getParentClass()) &&
               $parent->isUserDefined() &&
               ! isset(self::$seen[$parent->getName()])) {
            self::$seen[$parent->getName()] = true;
            array_unshift($classes, $parent);
        }

        if (function_exists('get_declared_traits')) {
            foreach ($classes as $c) {
                foreach (self::getTraits($c) as $trait) {
                    self::$seen[$trait->getName()] = true;
                    array_unshift($classes, $trait);
                }
            }
        }

        return array_merge(self::getInterfaces($class), $classes);
    }

    /**
     * @param   ReflectionClass $class
     * @return  array
     */
    public static function getTraits(ReflectionClass $class)
    {
        $traits = $class->getTraits();
        $classes = array();
        while ($trait = array_pop($traits)) {
            if ($trait->isUserDefined() && 
                !isset(self::$seen[$trait->getName()])) {
                $classes[] = $trait;

                $traits = array_merge($traits, $trait->getTraits());
            }
        }

        return $classes;
    }

    /**
     * @param   ReflectionClass $class
     * @return  array
     */
    public static function getInterfaces(ReflectionClass $class)
    {
        $classes = array();

        foreach ($class->getInterfaces() as $interface) {
            $classes = array_merge($classes, self::getInterfaces($interface));
        }

        if ($class->isUserDefined() && $class->isInterface() &&
        !isset(self::$seen[$class->getName()])) {
            self::$seen[$class->getName()] = true;

            $classes[] = $class;
        }

        return $classes;
    }
}
