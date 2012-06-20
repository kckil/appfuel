<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\App;

/**
 * Handle path information for the following
 * <base-path>  : absolute path to the applications root directory
 * www            : the web root directory
 * bin            : cli scripts
 * test            : unit test bootrapping and supporting files
 * package        : php source code
 * resource        : clientside resource files js,css,html,phtml etc...
 * routes        : route specification files 
 * config        : config files 
 * datasource    : mappings for database, webservices, files etc..
 * build        : system generated files    
 * 
 * Allows the application dir structure change without changing the
 * kernel code.
 */
interface AppDetailInterface
{
    /**
     * @return  string
     */
    public function getBasePath($path = null);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getBin($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getWWW($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getTest($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getPackage($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getResource($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getConfig($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getDataSource($isBase = true);

    /**
     * @param   bool    $isBase
     * @return  string
     */
    public function getBuild($isBase = true);
}
