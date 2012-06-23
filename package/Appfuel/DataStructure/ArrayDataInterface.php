<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\DataStructure;

/**
 * Manages a list of data, handling get/setting and checking items. This 
 * eliminates the illegal offet warning when dealing with arrays
 */
interface ArrayDataInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function getAll();

    /**                                                                          
     * @param   array   $data                                                    
     * @return  Dictionary                                                         
     */                                                                          
    public function setAll(array $data);                                        

    /**
     * @param   array    $data
     * @return  Dictionary
     */
    public function load(array $data);

    /**
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */  
    public function set($key, $value);

    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */ 
    public function add($key, $value);
    
    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */ 
    public function assign($key, $value);

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null);

    /**
     * Returns the item at $key only when it exists as $type otherwise return
     * $default
     *
     * @param   scalar  $key
     * @param   mixed   $type 
     * @param   mixed   $default 
     * @return  mixed
     */
    public function getWhen($key, $type, $default = null);

    /** 
     * select values for many keys at once
     *                                                                         
     * @param    array    $key
     * @param    array    $isArray                                               
     * @return    Dictionary                                                     
     */                                                                          
    public function collect(array $keys, $isArray = true);

    /** 
     * select values for many keys at once when they are a particular type
     *                                                                         
     * @param    array    $key
     * @param    array    $isArray                                               
     * @return    Dictionary                                                     
     */                                                                          
    public function collectWhen(array $keys, $type, $isArray = false);

    /**
     * @param   scalar $key
     * @return  bool
     */
    public function exists($key);

    /**
     * Answers the question does this exist and is it a particular type
     *
     * @param   string  $key
     * @param   mixed string|object  $type   type that thing should be
     * @return  bool
     */
    public function existsAs($key, $type);

    /**                                                                          
     * @return  ViewTemplate                                                     
     */                                           
    public function clear($key = null);
}
