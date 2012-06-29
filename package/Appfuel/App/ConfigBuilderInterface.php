<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

/**
 * Build a config file from merging two enviroment specific config files 
 * togather
 */
interface ConfigBuilderInterface
{
    /**
     * @param   array   $array
     * @return  string
     */
    //public function printArray(array $array);

    /**
     * @param   array   $array
     * @param   int     $level
     * @return  string
     */    
    //public function printArrayBody(array $array, $level = 0);
}
