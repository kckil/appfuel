<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Route;

interface RoutePatternSpecInterface
{
    /**
     * @return  string
     */
    public function getRouteKey();

    /**
     * @return  string
     */
    public function getPattern($method = null);

    /**
     * @return  string | null when not set
     */
    public function getGroup();
}
