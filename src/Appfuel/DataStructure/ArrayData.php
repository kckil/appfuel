<?php
/**                                                                              
 * Appfuel                                                                       
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for full details.
 */
namespace Appfuel\DataStructure;

use ArrayIterator,
    DomainException,
    InvalidArgumentException;

/**
 * Manages a list of data, handling get/setting and checking items. This 
 * eliminates the illegal offet warning when dealing with arrays
 */
class ArrayData implements ArrayDataInterface
{
    /**
     * Used to detemine the key validation strategy
     * @var string
     */
    protected $indexType = 'any';

    /**
     * List of items stored in the dictionary
     * @var array
     */
    protected $data = array();

    /**
     * @param   array   $data
     * @param   string  $type   type of index for the array
     * @return  Dictionary
     */
    public function __construct(array $data = null, $type = 'any')
    {
        if (null !== $data) {
            $this->load($data);
        }

        $this->setIndexType($type);
    }

    /**
     * @return  string
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @param   string  $type
     * @return  ArrayData
     */
    public function setIndexType($type)
    {
        if (! is_string($type) || empty($type)) {
            $err = "index type must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $valid = array('any', 'int', 'string', 'non-empty-string');
        if (! in_array($type, $valid, true)) {
            $types = implode(', ', $valid);
            $err = "valid typ must be -($types)";
            throw new DomainException($err);
        }

        $this->indexType = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @param   mixed   $offset
     * @param   mixed   $value
     * @return  null
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->append($value);
            return;
        }
        
        if (! $this->isValidKey($offset)) {
            $err = "offset key in not valid for -({$this->getIndexType()})";
            throw new DomainException($err);
        } 

        $this->data[$offset] = $value;
    }
    
    /**
     * @param   mixed   $offset
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->data[$offset] : null;
    }
    
    /**
     * @param   mixed   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        if (null === $offset ||
            ! is_scalar($offset) || 
            ! array_key_exists($offset, $this->data)) {
            return false;
        }

        return true;
    }

    /**
     * @param   mixed   $offset
     * @return  null
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->data[$offset]);
        }
    }

    /**
     * @return  ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data); 
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
     * @return  ArrayData                                                         
     */                                                                          
    public function setAll(array $data)                                          
    {                                                                            
        $this->clear();                                                          
        $this->load($data);                                                      
        return $this;                                                            
    }

    /**
     * @param   array    $data
     * @return  ArrayData
     */
    public function load(array $data)
    {
        foreach ($data as $key => $value) {
            $this->offsetSet($key, $value);
        }

        return $this;
    }

    /**
     * @param   string  $key
     * @param   mixed   $value    
     * @return  ArrayData
     */  
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  ArrayData
     */ 
    public function add($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Alias to set
     * 
     * @param   string  $key
     * @param   mixed   $value    
     * @return  ArrayData
     */ 
    public function assign($key, $value)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        if (! $this->offsetExists($key)) {
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
    public function getAs($key, $type, $default = null)
    {
        if (! $this->existsAs($key, $type)) {
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
    public function collectAs(array $keys, $type, $isArray = true)                
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
     * Alias to offsetExists
     *
     * @param   scalar $key
     * @return  bool
     */
    public function exists($key)
    {
        return $this->offsetExists($key);
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
        if (empty($type) || ! $this->offsetExists($key)) {
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
            case 'bool-true'  : $isType  = $item === true;      break;
            case 'bool-false' : $isType  = $item === false;     break;
            case 'non-empty-string': 
                $isType = ! empty($item) && is_string($item); 
                break;
            default        : 
                $isType  = $item instanceof $type;
        }

        return $isType;
    }

    /** 
     * @return  ArrayData 
     */ 
    public function clear($key = null) 
    { 
        if (null === $key) { 
            $this->data = array(); 
            return $this; 
        } 

        $this->offsetUnset($key); 
        return $this;
    }

    /**
     * @return  null
     */
    public function serialize()
    {
        return serialize($this->data);
    }

    /**
     * @param   string  $data
     * @return  null
     */
    public function unserialize($data)
    {
        $this->data = unserialize($data);
    }

    /**
     * @param   mixed   $key
     * @return  bool
     */
    protected function isValidKey($key)
    {
        if (null === $key || ! is_scalar($key)) {
            return false;
        }

        $type = $this->getIndexType();
        switch($type) {
            case 'any':
                $result = true;
                break;
            case 'int':
                $result = is_int($key);
                break;
            case 'string':
                $result = is_string($key);
                break;
            case 'non-empty-string':
                $result = is_string($key) && strlen($key) > 0;
                break;
            default:
                $result = false;
        }

        return $result;
    }
}
