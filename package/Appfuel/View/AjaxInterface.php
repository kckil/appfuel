<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\View;

/**
 * Interface needed to service ajax calls from the application.
 */
interface AjaxInterface extends ViewInterface
{
    /**
     * @return  string
     */
    public function getStatusCode();
    
    /**
     * @param   scalar  $code
     * @return  AjaxTemplateInterface
     */
    public function setStatusCode($code);
    
    /**
     * @return  string
     */
    public function getStatusText();
    
    /**
     * @param   string  $text
     * @return  AjaxTemplateInterface
     */
    public function setStatusText($text);
    
    /**
     * @param   scalar    $code
     * @param   string    $text
     * @return  AjaxTemplateInterface
     */
    public function setStatus($code, $text);
}
