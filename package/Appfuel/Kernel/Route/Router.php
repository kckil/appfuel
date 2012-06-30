<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
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
     * @param   string  $uri
     * @return  array | false
     */
    static public function findRoute($uri)
    {
        if (! is_string($uri)) {
            $err = "request uri must be a string";
            throw new DomainException($err);
        }

        $uri = ltrim($uri, "/");
        $group = self::resolveGroup($uri);
        $patterns = RouteRegistry::getPatterns($group['group']);
        
        $matches  = array();
        $routeKey = null;
        $patternUri = $group['uri'];
        foreach ($patterns as $key => $pattern) {
            if (preg_match($pattern, $patternUri, $matches)) {
                $routeKey = $key;
                break;
            }
            $matches = array();
        }

        if (null === $routeKey) {
            return false;
        }

        return array(
            'uri'           => $uri,
            'group'         => $group['group'],
            'group-match'   => $group['matched'],
            'group-capture' => $group['captured'],
            'route-key'     => $routeKey,
            'route-match'   => array_shift($matches),
            'route-capture' => $matches
        );        
    }

    /**
     * @param   string  $uri
     * @return  array
     */
    static public function resolveGroup($uri)
    {
        if (! is_string($uri)) {
            $err = "request uri must be a string";
            throw new DomainException($err);
        }
        
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
            return array(
                'matched'      => null,
                'group'        => 'no-group',
                'uri'          => $uri,
                'captured'     => array()             
            );
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
        $newUri = ltrim(substr($uri, $pos), '/');
        return array(
            'original-uri' => $uri,
            'matched'      => $matched,
            'group'        => $group,
            'uri'          => $newUri,
            'captured'     => $matches
        );
    }
}
