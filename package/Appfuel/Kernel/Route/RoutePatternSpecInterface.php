<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

/**
 * Value object used to hold the route key, regex pattern, and group for a 
 * given route. It is used by the route manager to process the uri matching it
 * to the correct route.
 */
interface RoutePatternSpecInterface
{
    /**
     * @return  string
     */
    public function getRouteKey();

    /**
     * @return  string
     */
    public function getRegEx();

    /**
     * @return  string | null when not set
     */
    public function getGroup();
}
