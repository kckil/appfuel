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
class ArrayData implements ArrayDataInterface
{
    /**
     * List of items stored in the dictionary
     * @var array
     */
    protected $data = array();

    /**
     * @param   array    $data
     * @return  Dictionary
     */
    public function __construct(array $data = null)
    {
        if (null !== $data) {
            $this->load($data);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**                                                                          
     * @param   array   $data                                                    
     * @return  Dictionary                                                         
     */                                                                          
    public function setAll(array $data)                                          
    {                                                                            
        $this->clear();                                                          
        $this->load($data);                                                      
        return $this;                                                            
    }

    /**
     * @param   array    $data
     * @return  Dictionary
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->add($key, $value);
        }

        return $this;
    }

    /**
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */  
    public function set($key, $value)
    {
        if (! is_scalar($key)) {
            throw new InvalidArgumentException("key must be a scalar value");
        }

        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */ 
    public function add($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  Dictionary
     */ 
    public function assign($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        if (! $this->exists($key)) {
            return $default;
        }

        return $this->data[$key];
    }

    /**
     * Returns the item at $key only when it exists as $type otherwise return
     * $default
     *
     * @param   scalar  $key
     * @param   mixed   $type 
     * @param   mixed   $default 
     * @return  mixed
     */
    public function getWhen($key, $type, $default = null)
    {
        if (! $this->existAs($key, $type)) {
            return $default;
        }

        return $this->data[$key];
    }

    /** 
     * select values for many keys at once
     *                                                                         
     * @param    array    $key
     * @param    array    $isArray                                               
     * @return    Dictionary                                                     
     */                                                                          
    public function collect(array $keys, $isArray = true)                
    {                                                                            
        $result = array();                                                       
        foreach ($keys as $key) {                                                
            if (! $this->exists($key)) {                                          
                continue;                                                        
            }
                                                   
            $result[$key] = $this->get($key);                                              
        }                                                                        
                                                                                 
        if (false === $isArray) {                                                 
            return new self($result);                                                      
        }                                                                        
                                                                                 
        return $result;                                          
    }

    /** 
     * select values for many keys at once when they are a particular type
     *                                                                         
     * @param    array    $key
     * @param    array    $isArray                                               
     * @return    Dictionary                                                     
     */                                                                          
    public function collectWhen(array $keys, $type, $isArray = false)                
    {                                                                            
        $result = array();                                                       
        foreach ($keys as $key) {                                                

            if (! $this->existsAs($key, $type)) {                                          
                continue;                                                        
            }
                                                   
            $result[$key] = $this->get($key);                                            
        }                                                                        
                                                                                 
        if (true === $isArray) {                                                 
            return $result;                                                      
        }                                                                        
                                                                                 
        return new self($result);                              
    }

    /**
     * @param   scalar $key
     * @return  bool
     */
    public function exists($key)
    {
        if (! is_scalar($key) || ! array_key_exists($key, $this->data)) {
            return false;
        }

        return true;
    }

    /**
     * Answers the question does this exist and is it a particular type
     *
     * @param   string  $key
     * @param   mixed string|object  $type   type that thing should be
     * @return  bool
     */
    public function existsAs($key, $type)
    {   
        if (! empty($type) || ! $this->exists($key)) {
            return false;
        }

        $item = $this->data[$key];

        switch ($type) {
            case 'array'   : $isType  = is_array($item);        break;
            case 'bool'    : $isType  = is_bool($item);         break;
            case 'float'   : $isType  = is_float($item);        break;
            case 'int'     : $isType  = is_int($item);          break;
            case 'numeric' : $isType  = is_numeric($item);      break;
            case 'scalar'  : $isType  = is_scalar($item);       break;
            case 'object'  : $isType  = is_object($item);       break;
            case 'string'  : $isType  = is_string($item);       break;
            case 'resource': $isType  = is_resource($item);     break;
            case 'callable': $isType  = is_callable($item);     break;
            case 'null'    : $isType  = is_null($item);         break;
            case 'empty'   : $isType  = empty($item);           break;
            case 'bool-true'  : $isType  = $item === true;       break;
            case 'bool-false' : $isType  = $item === false;      break;
            case 'non-empty-string': 
                $isType = ! empty($item) && is_string($item); 
                break;
            default        : 
                $isType  = $item instanceof $type;
        }

        return $isType;
    }

    /**                                                                          
     * @return  ViewTemplate                                                     
     */                                                                          
    public function clear($key = null)                                           
    {                                                                            
        if (null === $key) {                                                     
            $this->data = array();                                               
            return $this;                                                        
        }                                                                        
                                                                                 
        if ($this->exists($key)) {                                               
            unset($this->data[$key]);                                            
        }                                                                        
        return $this;                                                            
    }
}
