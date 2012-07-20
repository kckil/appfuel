<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use InvalidArgumentException,
    Appfuel\Kernel\FaultHandler;

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

    /**
     * @return  AppInitializer
     */
    public function showErrors()
    {
        ini_set('display_errors', '1');
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function hideErrors()
    {
        ini_set('display_errors', '0');
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function disableErrorReporting()
    {
        error_reporting(0);
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function enableFullErrorReporting()
    {
        error_reporting(-1);
        return $this;
    }

    /**
     * @param   int $level
     * @return  AppInitializer
     */
    public function setErrorReporting($level)
    {
        if (! is_int($level)) {
            throw new InvalidArgumentException("error level must be an int");
        }

        error_reporting($level);
        return $this;
    }

    /**
     * @return  AppInitiailzer
     */
    public function enableDebugging()
    {
        $this->showErrors()
             ->enableFullErrorReporting();

        return $this;
    }

    /**
     * @param   callable    $handler
     * @return  AppInitializer
     */
    public function registerExceptionHandler($handler)
    {
        set_exception_handler($handler);
        return $this;
    }

    /**
     * @param   callable    $handler
     * @param   int         $errorTypes
     * @return  AppInitializer
     */
    public function registerErrorHandler($handler, $types = null)
    {
        set_error_handler($handler, $types);
        return $this;
    }

    /**
     * @return  AppInitializer
     */
    public function registerAppFuelFaultHandler()
    {
        $handler = new FaultHandler();
        $this->registerExceptionHandler(array($handler, 'handleException'))
             ->registerErrorHandler(array($handler, 'handleError'));

        return $this;
    }
}
