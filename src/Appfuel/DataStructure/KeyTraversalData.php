<?php
/**                                                                              
 * Appfuel                                                                       
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for full details.
 */
namespace Appfuel\DataStructure;

use LogicException,
    DomainException,
    InvalidArgumentException;

class KeyTraversalData extends ArrayData
{
    const KEY_NOT_FOUND = '__KeyTraversalData_KEY_NOT_FOUND__';

    /**
     * Delimitor used in the key to encode an array hierarchy
     * @var string
     */
    protected $delim = '.';

    /**
     * Used to detemine the key validation strategy
     * @var string
     */
    protected $indexType = 'non-empty-string';

    /**
     * @param   array   $data
     * @param   string  $type   type of index for the array
     * @return  Dictionary
     */
    public function __construct(array $data = null, $delim = null)
    {
        if (null !== $data) {
            $this->load($data);
        }

        if (null !== $delim) {
            $this->setDelimitor($delim);
        }
    }

    /**
     * @return  string
     */
    public function getDelimitor()
    {
        return $this->delim;
    }

    /**
     * @param   string  $char
     * @return  ArrayData
     */
    public function setDelimitor($char)
    {
        if (! is_string($char) || empty($char)) {
            $err = "array delimitor must be a non empty string";
            throw new InvalidArgumentException($err);
        }
    
        $this->delim = $char;
        return $this;
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isDelimitor($key, $delim)
    {
        if (false === $pos = strpos($offset, $delim)) {
            false;   
        }
            
        return true;
    }

    /**
     * @param   string  $type
     * @return  ArrayData
     */
    public function setIndexType($type)
    {
        $err = "this structure only supports not empty strings as keys";
        throw new InvalidArgumentException($err);
    }

    /**
     * @param   mixed   $offset
     * @param   mixed   $value
     * @return  null
     */
    public function offsetSet($offset, $value)
    {
        if (! $this->isValidKey($offset)) {
            $err  = "offset key is not valid for an index type of ";
            $err .= "{$this->getIndexType()}";
            throw new DomainException($err);
        } 

        $delim = $this->getDelimitor();
        if (! $this->isDelimitor($offset, $delim)) {
            $this->data[$offset] = $value;
            return;
        }

        if (false === $keys = explode($delim, $offset)) {
            $err  = "can not assign: delimitor -($delim) can not be exploded ";
            $err .= "from key -($offset)";
            throw new LogicException($err);
        }

        /*
         * The key we need to assign the value to is the item in the list
         */
        $targetKey = array_pop($keys);

        /*
         * Walk down the list of items traversing arrays, ArrayAccess objects
         * or objects that act like arrays with exists and get methods
         */
        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            }
            else if ($data instanceof ArrayAccess && $data->offsetExists($key)){
                $data = $data[$key];
            }
            else if (is_object($data) && 
                     method_exists($data, 'exists') &&
                     method_exists($data, 'get') &&
                     $data->exists($key)) {
                $data = $data->get($key);
            }
            else {
                $err = "could not assign: key in the chain -($key) not found";
                throw new LogicException($err);
            }
        }

        if (is_array($data) || $data instanceof ArrayAccess) {
            $data[$targetKey] = $value;
        }
        else if (is_object($data) && method_exists($object, 'set')) {
            $data->set($targetKey, $value);
        }
        else {
            $err = "data was found at -($key) but it was not an array or ";
            $err = "an object that implements ArrayAccess or a method 'set'";
            throw new LogicException($err);
        }
    }
    
    /**
     * @param   mixed   $offset
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        if (! $this->isValidKey($offset)) {
            return self::KEY_NOT_FOUND;
        }

        $delim = $this->getDelimitor();
        if (! $this->isDelimitor($offset, $delim)) {
            if (! array_key_exists($offset, $this->data)) {
                return self::KEY_NOT_FOUND;
            }
            return $this->data[$offset];
        }

        if (false === $keys = explode($delim, $offset)) {
            return self::KEY_NOT_FOUND;
        }

        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            }
            else if ($data instanceof ArrayAccess && $data->offsetExists($key)){
                $data = $data[$key];
            }
            else if (is_object($data) && 
                     method_exists($data, 'exists') &&
                     method_exists($data, 'get') &&
                     $data->exists($key)) {
                $data = $data->get($key);
            }
            else {
                return  self::KEY_NOT_FOUND;
            }
        }

        return $data;
    }
    
    /**
     * @param   mixed   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        if (! $this->isValidKey($offset)) {
            return false;
        }
 
        $delim = $this->getDelimitor();
        if (! $this->isDelimitor($offset, $delim)) {
            if (! array_key_exists($offset, $this->data)) {
                return false;
            }
            return true;
        }

        if (false === $parts = explode($delim, $offset)) {
            return false;
        }
        
        $data = $this->data; 
        foreach ($parts as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            }
            else if ($data instanceof ArrayAccess && $data->offsetExists($key)){
                $data = $data[$key];
            }
            else if (is_object($data) && 
                     method_exists($data, 'exists') &&
                     method_exists($data, 'get') &&
                     $data->exists($key)) {
                $data = $data->get($key);
            }
            else {
                return false;
            }
        }

        return true;
    }

    /**
     * @param   mixed   $offset
     * @return  null
     */
    public function offsetUnset($offset)
    {
        if (! $this->isValidKey($offset)) {
            $err  = "offset key is not valid for an index type of ";
            $err .= "{$this->getIndexType()}";
            throw new DomainException($err);
        } 

        $delim = $this->getDelimitor();
        if (! $this->isDelimitor($offset, $delim)) {
            unset($this->data[$offset]);
            return;
        }

        if (false === $keys = explode($delim, $offset)) {
            $err  = "can not unset: delimitor -($delim) can not be exploded ";
            $err .= "from key -($offset)";
            throw new LogicException($err);
        }

        /*
         * The key we need to assign the value to is the item in the list
         */
        $targetKey = array_pop($keys);

        /*
         * Walk down the list of items traversing arrays, ArrayAccess objects
         * or objects that act like arrays with exists and get methods
         */
        $data = $this->data;
        foreach ($keys as $key) {
            if (is_array($data) && array_key_exists($key, $data)) {
                $data = $data[$key];
            }
            else if ($data instanceof ArrayAccess && $data->offsetExists($key)){
                $data = $data[$key];
            }
            else if (is_object($data) && 
                     method_exists($data, 'exists') &&
                     method_exists($data, 'get') &&
                     $data->exists($key)) {
                $data = $data->get($key);
            }
            else {
                $err = "could not unset: key in the chain -($key) not found";
                throw new LogicException($err);
            }
        }

        if (is_array($data) || $data instanceof ArrayAccess) {
            unset($data[$targetKey]);
        }
        else if (is_object($data) && method_exists($object, 'clear')) {
            $data->clear($targetKey);
        }
        else {
            $err = "data was found at -($key) but it was not an array or ";
            $err = "an object that implements ArrayAccess or a method 'clear'";
            throw new LogicException($err);
        }
    }

    /**
     * @throws  LogicException
     *
     * @param   mixed   $value
     * @return  ArrayData
     */
    public function append($value)
    {
        throw new LogicException("append does not work with string keys");
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = null)
    {
        if (self::KEY_NOT_FOUND === $data = $this->offsetGet($key)) {
            return $default;
        }

        return $data;
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

        return $this->offsetGet($key);
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
            $item = $this->offsetGet($key);
            if (self::KEY_NOT_FOUND === $item) { 
                continue; 
            }

            $result[$key] = $item; 
        } 

        if (false === $isArray) { 
            return new self($result); 
        } 

        return $result; 
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
        $item = $this->offsetGet($key);
        if (empty($type) || self::KEY_NOT_FOUND === $item) {
            return false;
        }

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
