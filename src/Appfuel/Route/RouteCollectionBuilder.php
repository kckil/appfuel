<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use DomainException,
    OutOfBoundsException,
    InvalidArgumentException,
    Appfuel\DataStructure\ArrayPrinter;

class RouteCollectionBuilder implements RouteCollectionBuilderInterface
{
    /*
     * List of strings key by the root route key and ordered by the depth
     * of the key. Each string represents the code to instantiate a single
     * action route
     */
    protected $routes = array();

    /**
     * @param   array   $list
     * @return  RouteCollection
     */
    public function createRouteCollection(array $list = null)
    {
        if (null !== $list) {
            foreach ($list as $data) {
                $this->loadRoute($data);
            }
        }
   
        sort($this->routes, SORT_NUMERIC);
        $collection = new RouteCollection();
        foreach ($this->routes as $level => $routeList) {
            foreach ($routeList as $route) {
                $collection->add($route);
            }
        }

        return $collection;
    }

    /**
     * @param   array   $data
     * @return  string
     */
    public function loadRoute(array $data)
    {
        /*
         * we create a route spec to validate the data, we still need to 
         * compile the raw regex
         */
        $spec = new RouteSpec($data);
        $pattern = $this->compileRegex($spec->getPattern());
        $result = $this->validateRegex($pattern);
        if (true !== $result) {
            $err  = "regex pattern failed: $result ";
            $err .= "original -({$spec->getPattern()}) compiled -($pattern)";
            throw new DomainException($err);
        }

        /* this is now the complete spec data */
        $data['pattern'] = $pattern;
        $spec = new RouteSpec($data);
         
        $key = $spec->getKey();
        $level = substr_count($key, '.');
        $this->routes[$level][$key] = new ActionRoute($spec);
        
        return $this;
    }

    /**
     * @return  array
     */
    public function getRouteStrings()
    {
        return $this->routes;
    }

    /**
     * @return string
     */
    public function getRouteString($key)
    {
        if (! is_string($key) || empty($key)) {
            $err = "route key must be a non empty string";
            throw new DomainException($err);
        }
        
        if (! isset($this->routes[$key])) {
            return false;
        }

        return $this->routes[$key];
    }

    /**
     * @param   mixed   string | array
     * @return  string
     */
    public function compileRegex($raw)
    {
        $modifiers = '';
        if (is_array($raw) && isset($raw[0]) && isset($raw[1])) {
            if (! is_string($raw[0]) || ! is_string($raw[1])) {
                $err = "both items in the regex array must be valid strings";
                throw new OutOfBoundsException($err);
            }
            $modifiers = $raw[1];
            $raw = $raw[0];
        }
        else if (! is_string($raw)) {
            $err  = "raw regex must be a string or an array where ";
            $err .= "the first item is the regex and the second is a string ";
            $err .= "of pattern modifiers";
            throw new InvalidArgumentException($err);
        }

        if (! preg_match('{\\\\(?:#|$)}', $raw)) { 
            $clean = preg_replace('!#!', '\#', $raw); 
        } 
        else { 
            $pattern = '{  [^\\\\#]+  |  \\\\. |  ( \#   |  \\\\$  )  }sx';
            $clean = preg_replace_callback($pattern, function ($matches) { 
                if (empty($matches[1])) {
                    return $matches[0];
                }

                return '\\\\' . $matches[1]; 
            }, $raw); 
        }

        return "#$clean#$modifiers";
    }

    /**
     * @param   string  $regex
     * @return  true | string (error message when regex fails)
     */
    public function validateRegex($regex) 
    { 
        /* 
         * To tell if the pattern has errors, we try to use it 
         */ 
        if ($oldTrack = ini_get("track_errors")) { 
           $oldMsg = isset($php_errormsg) ? $php_errormsg : false; 
        } 
        else { 
            ini_set('track_errors', 1); 
        }
 
        /* no that we backup the old message and ensured track_errors is 
         * enabled we are ready to try out the regex 
         */ 
        unset($php_errormsg); 

        @preg_match($regex, ""); 

        $result = isset($php_errormsg) ? $php_errormsg : false; 

        /*                                                                       
         * restore the global state now that we have what we are after           
         */                                                                      
        if ($oldTrack) { 
            $php_errormsg = isset($oldMsg) ? $oldMsg : false; 
        } 
        else { 
            ini_set('track_errors', 0); 
        } 

        if (false !== $result) { 
            return $result; 
        } 

        return true; 
    }
}
