<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;


interface MatchedRouteInterface
{
    /**
     * @return  string
     */
    public function getKey();

    /**
     * @return  string
     */
    public function getController();

    /**
     * @return  array
     */
    public function getCaptures();
}
