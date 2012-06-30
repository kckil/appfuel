<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use Exception,
    DomainException,
    Appfuel\ClassLoader\NamespaceParser;

/**
 * There are many routing objects used to controll the framework. Each routing
 * routing object is a value object (or close to it) that requires an array
 * of configuration data in its constructor. I encode the class name and 
 * interface in order to simply the creation to a single method of all objects.
 * Class Encoding: Route[strategy] and Route[strategy]Interface 
 *                   where strategy is converted to proper case
 */
class RouteFactory
{
    /**
     * @param   array   $types
     * @param   array   $spec
     * @return  array
     */
    static public function createRouteSpecs(array $types, array $spec)
    {
        $list = array();
        foreach ($types as $key => $strategy) {
            $list[$key] = self::createRouteSpec($strategy, $spec); 
        }

        return $list;
    }

    /**
     * @param   string  $cat
     * @param   array   $data
     * @return  mixed
     */
    static public function createRouteSpec($strategy, array $spec)
    {
        if (! is_string($strategy) || empty($strategy)) {
            $err = "route strategy must be a non empty string";
            throw new DomainException($err);
        }
        
        $classKey  = 'route-' . strtolower($strategy) . '-spec-override';
        $strategy  = ucfirst($strategy);
        $class     = __NAMESPACE__ . "\\Route{$strategy}Spec";
        $interface = "{$class}Interface";

        /*
         * before creating the route object look into the specification to see
         * if the user as defined a class override and use that instead
         */
        if (isset($spec[$classKey])) {
            $class = $spec[$classKey];
            if (! is_string($class) || empty($class)) {
                $err = "invalid route class: must be a non empty string";
                throw new DomainException($err);
            }
        }

        try {
            $object = new $class($spec);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $err = "route factory could not create -($class): $msg";
            throw new DomainException($err);
        }

        if (! $object instanceof $interface) {
            $err = "route object -($class) does not implement -($interface)";
            throw new DomainException($err);
        }

        return $object;
    }
}
