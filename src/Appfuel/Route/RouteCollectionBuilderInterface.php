<?php 
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

interface RouteCollectionBuilderInterface
{
    /**
     * @param   mixed   string | array
     * @return  string
     */
    public function compileRegex($raw);

    /**
     * @param   string  $regex
     * @return  true | string (error message when regex fails)
     */
    public function validateRegex($regex);
}
