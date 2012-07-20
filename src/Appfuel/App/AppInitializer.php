<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use LogicException,
    DomainException;

/**
 * Application initialization involves error display, error level, register
 * error and exception handling and moving configuration data into the 
 * settings registry.
 */
class AppInitializer implements AppInitializerInterface
{
    /**
     * @var string
     */
    protected $env = null;


    /**                                                                          
     * @param   string  $env                                                     
     * @return  AppInitializer                                                      
     */                                                                          
    public function __construct($env)                                            
    {                                                                            
        if (! is_string($env) || empty($env)) {                                  
            $err = "environment name must be a non empty string";                
            throw new InvalidArgumentException($err);                            
        }

        $this->env = $env;
    }

    /**
     * @return  string
     */
    public function getEnv()
    {
        return $this->env;
    }
}
