<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use LogicException,
    DomainException,
    InvalidArgumentException;
/**
 * The router will attempt to match a uri string against any url group patterns
 * defined in the url-groups file, when no groups are found a group of 
 * 'no-group' will be assigned. During the build, all routes are compiled into
 * a list of groups and those having no groups into 'no-group'. Once grouping
 * is resolved a list of route patterns is collected and matched against. 
 * when a match is found the following array is returned other it will be false
 * 
 * Route: array(
 *  'uri'           => [string: the original uri],
 *  'group'         => [string: name of the group],
 *  'group-match'   => [string: text matched with url group regex],
 *  'group-captures'=> [array: list of regex captures for group],
 *  'route-key'     => [string: resolved route key],
 *  'route-match'   => [string: text matched with route regex],
 *  'route-captures'=> [array: list of regex captures for route],
 * );
 */
class Router
{
    /**
     * @param   array
     */
    static private $route = array(
        'original-uri'   => null,
        'uri'            => null,
        'format'         => null,
        'group'          => null,
        'group-match'    => null,
        'group-captures' => array(),
        'route-key'      => null,
        'route-match'    => null,
        'route-captures' => array(),
        'unmatched'      => null,
        'final-captures' => array(),
    );

    /**
     * @param   string  $uri
     * @return  array | false
     */
    static public function findRoute($uri, $method, $isFormat = true)
    {
        self::sanitize($uri, $isFormat);
        self::resolveGroup();
        if (! self::resolveRoute($method)) {
            return false;
        }

        self::captureUnmatched();
        self::resolveCaptures();

        return RouteFactory::createMatchedRoute(self::$route);
    }

    /**
     * @param   string  $group  
     * @param   string  $uri
     * @return  string|false
     */
    static protected function resolveRoute($method)
    {
        if (! is_string($method) || empty($method)) {
            $err  = "request method must be a non empty string like ";
            $err .= "get,post,put,delete or even cli";
            throw new DomainException($err);
        }

        $group = self::$route["group"];
        if (null === $group) {
            $group = 'no-group';
        }
        $patterns = RouteRegistry::getPatterns($group);

        $uri = self::$route["uri"];
        $matches = array();
        $routeKey = null;
        foreach ($patterns as $key) {
            $uriSpec = RouteRegistry::getRouteSpec('pattern', $key);
            if (! $uriSpec instanceof RoutePatternSpecInterface) {
                $err = "uri specification for route -($key) not found";
                throw new LogicException($err);
            }

            $pattern = $uriSpec->getPattern($method);

            /*
             * the uri spec can hold a pattern as string or an array where
             * the first elmement is the pattern and the second is the regex
             * modifier string
             */
            $modifiers = null;
            if (is_array($pattern)) {
                $modifiers = isset($pattern[1]) ? $pattern[1] : null;
                $pattern = current($pattern);
            }
            
            /*
             * No pattern for this request method, move onto the next pattern
             */
            if (! is_string($pattern)) {
                continue;
            }
            $pattern = '#' . $pattern . "#{$modifiers}";
            if (preg_match($pattern, $uri, $matches)) {
                $routeKey = $key;
                break;
            }
            $matches = array();
        }

        if (null === $routeKey) {
            self::$route['unmatched'] = $uri;
            return false;
        }

        self::$route['route-key'] = $routeKey;
        self::$route['route-match'] = array_shift($matches);
        self::$route['route-captures'] = $matches;

        return true; 
    }

    /**
     * @return null
     */
    static protected function resolveCaptures()
    {
        $groupCaptures = self::$route['group-captures'];
        $routeCaptures = self::$route['route-captures'];

        $spec = RouteRegistry::getRouteSpec('uri', self::$route['route-key']);
        $params = $spec->getPathParams();
   
        $result = array();
        $named  = array();
        foreach ($routeCaptures as $key => $item) {
            $type = gettype($key);
            if ('string' === $type) {
                $named[$key] = $item;
                continue;
            }
    
            if (isset($params[$key])) {
                $captureKey = $params[$key];
                $result[$captureKey] = $item;
            }
        }

        $result = array_merge($groupCaptures, $result, $named);

        $defaults = $spec->getDefault();
        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $result)) {
                $result[$key] = $default;
                continue;
            }

            if ('' === $result[$key]) {
                $result[$key] = $default;
            }
        }

        self::$route['final-captures'] = $result;
    }

    /**
     * @return  null
     */
    static protected function captureUnmatched()
    {
        $uri = self::$route['uri'];
        $groupLen = strlen(self::$route['group-match']);
 
        /*
         * the parameter string, the remain part of the uri after group and 
         * route patterns have been matched, can be treated as optional 
         * name/value pairs that are not part of the pattern capture
         */ 
        $pos = $groupLen + strlen(self::$route['route-match']);
        $unmatched = substr($uri, $pos);
        if (is_string($unmatched) && ! empty($unmatched)) {
            self::$route['unmatched'] = $unmatched;
        }
    }

    /**
     * @param   string  $uri
     * @return  array
     */
    static protected function resolveGroup()
    {
        $uri = self::$route['uri'];
        $groups  = RouteRegistry::getGroupMap(); 
        $matches = array();
        $group   = null;
        foreach($groups as $pattern => $name) {
            if (preg_match($pattern, $uri, $matches)) {
                $group = $name;
                break;
            }

            /* reset matches for next preg_match attempt */
            $matches = array();
        }

        /*
         * no group matched in the uri
         */
        if (null === $group) {
            self::$route['group'] = null;
        }
        
        /*
         * preg_match puts the matched text as the first item and all
         * captures after it. 
         */
        $matched = array_shift($matches);
        
        /*
         * because another match will be attempted after the group we
         * will remove the matched text so url patterns that belong to groups
         * don't have to specify the group text in there regexs.
         */
        $pos = strpos($uri, $matched) + strlen($matched);
        self::$route['group-match'] = $matched;
        self::$route['group-captures'] = $matches;
        self::$route['group'] = $group;
        self::$route['uri'] = ltrim(substr($uri, $pos), '/');
    }

    /**
     * @param   string  $uri
     * @return  array
     */
    static protected function resolveFormat($uri)
    {
        $matches = array();
        $flags = PREG_OFFSET_CAPTURE;
        if (preg_match('/\.[-+a-zA-Z0-9_]+$/i', $uri, $matches, $flags)) {
            $matches = current($matches);
            $format  = ltrim($matches[0], '.');
            return array(substr($uri, 0, $matches[1]), $format);
        }

        return array($uri, null);
    }

    /**
     * @param   string  $uri
     * @pram    bool    $isFormat
     * @return  bool
     */
    static protected function sanitize($uri, $isFormat = true)
    {
        if (! is_string($uri)) {
            $err = "route uri must be a string";
            throw new DomainException($err);
        }

        $uri = ltrim($uri, "/");
        $format = null;
        if (true === $isFormat) {
            $result = self::resolveFormat($uri);
            $uri    = current($result);
            $format = next($result);
        }
        self::$route['uri'] = $uri;
        self::$route['format'] = $format;
        self::$route['original-uri'] = $uri;

        return $uri;
    }
}
