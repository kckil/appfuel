<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\Kernel\Task;

use DomainException,
    RunTimeException,
    Appfuel\ClassLoader\AutoLoaderInterface;

/**
 * register php autoloader
 */
class PHPAutoloaderTask extends StartupTask
{
    /**
     * @var array
     */
    protected $keys = array('php-autoloader');

    /**
     * @param   array    $params
     * @return  null
     */
    public function execute()
    {
        $params = $this->getParamData();
        if (! defined('AF_CODE_PATH')) {
            $err  = "the absolute path to the directory where all php ";
            $err .= "namespaces are found must be defined in a constant ";
            $err .= "named AF_CODE_PATH";
            throw new RunTimeException($err);
        }

        $loader = $params->get('php-autoloader');
        if (is_string($loader)) {
            $autoLoader = new $loader();
            if (! $autoLoader instanceof AutoLoaderInterface) {
                $err  = "loader -($loader) must implement Appfuel\ClassLoader";
                $err .= "\AutoLoaderInterface";
                throw new DomainException($err);
            }

            $loader->addPath(AF_CODE_PATH);
            $loader->register();
        }
        else if (is_array($loader)) {
            $func = current($loader);
            if (null === $func) {
                spl_autoload_register();
            }
            else {
                $isThrow   = (false === next($data)) ? false : true;
                $isPrepend = (true === next($data))  ? true  : false;
                spl_autoload_register($func, $isThrow, $isPrepend);
            }
        }

        return true;
    }
}
