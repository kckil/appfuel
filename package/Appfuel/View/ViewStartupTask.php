<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\View;

use DomainException,
    InvalidArgumentException,
    Appfuel\Kernel\StartupTask;

/**
 * Allow configuration for the resource management.  This will configure
 * the base url that serves up resources as well as toggling wether
 * the resources should be combo'd together or loaded individually.
 */
class ViewStartupTask extends StartupTask
{
    /**
     * Assign the registry keys to be pulled from the kernel registry
     * 
     * @return  OrmStartupTask
     */
    public function __construct()
    {
        $this->setRegistryKeys(array('clientside' => array()));
    }
    
    /**
     * @param   array    $params
     * @return  null
     */
    public function execute(array $params = null)
    {
        if ('cli' === PHP_SAPI) {
            return;
        }
        $data   = $params['clientside'];
        
        $scheme = 'http://';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) {
            $scheme = 'https://';
        } 
        
        $isCdn = false;
        if (isset($data['is-cdn']) && true === $data['is-cdn']) {
            if (! isset($data['cdn-url']) || 
                ! is_string($data['cdn-url']) ||
                empty($data['cdn-url'])) {
                $err = "cdn url is enbled but is not a string or is empty";
                throw new DomainException($err);
            }            
            
            $url = $data['cdn-url'];
        
        }
        else if (isset($_SERVER['HTTP_HOST']))  {
            $url = $_SERVER['HTTP_HOST'];
        }
        else {
            $err  = "could not create a url for client side resources ";
            $err .= "cdn was not set and HTTP_HOST is not in the server ";
            $err .= "super global";
            throw new DomainException($err);
        }
       
        $url = $scheme . $url;

        $isBuild = true;
        if (isset($data['is-build']) && false === $data['is-build']) {
            $isBuild = false;
        }
        
        if (! defined('AF_RESOURCE_URL')) {
            define('AF_RESOURCE_URL', $url);
        }
        
        if (! defined('AF_IS_RESOURCE_BUILD')) {
            define('AF_IS_RESOURCE_BUILD', $isBuild);
        }
    }
}
