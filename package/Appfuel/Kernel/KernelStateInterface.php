<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\Kernel;

/**
 * Value object used to hold the current state of the frameworks environment 
 * settings.
 */
interface KernelStateInterface
{
    /**
     * @return  string
     */
    public function getDisplayError();

    /**
     * @return  string
     */
    public function getErrorReporting();

    /**
     * @return  string
     */
    public function getDefaultTimezone();

    /**
     * @return  string
     */
    public function getIncludePath();

    /**
     * @return  bool
     */
    public function isAutoloadEnabled();

    /**
     * @return  bool
     */
    public function getAutoloadStack();
}
