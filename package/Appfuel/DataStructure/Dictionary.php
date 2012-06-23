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
 * Deprecated: use ArrayData instead.
 */
class Dictionary implements DictionaryInterface
{
    /**
     * Items stored in the bag
     * @var array
     */
    protected $items = array();

    /**
     * @param   array    $data
     * @return  Dictionary
     */
    public function __construct(array $data = null)
    {
        if (null === $data) {
            $data = array();
        }

        $this->load($data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param   string    $key    item key
     * @param   mixed    $value    
     * @return  Dictionary
     */    
    public function add($key, $value)
    {
        if (! is_scalar($key)) {
            return $this;
        }

        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @param   string    $key
     * @param   mixed    $default        return value when not found
     * @return  mixed
     */
    public function get($key, $default = NULL)
    {
        if (! array_key_exists($key, $this->items)) {
            return $default;
        }

        return $this->items[$key];
    }

    /**
     * @param   string $key
     * @return  bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->items);
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
        if (empty($type) || ! $this->exists($key)) {
            return false;
        }

        $item = $this->items[$key];

        switch ($type) {
            case 'array'   : $isType  = is_array($item);    break;
            case 'bool'    : $isType  = is_bool($item);     break;
            case 'float'   : $isType  = is_float($item);    break;
            case 'int'     : $isType  = is_int($item);      break;
            case 'numeric' : $isType  = is_numeric($item);  break;
            case 'scalar'  : $isType  = is_scalar($item);   break;
            case 'object'  : $isType  = is_object($item);   break;
            case 'string'  : $isType  = is_string($item);   break;
            case 'resource': $isType  = is_resource($item); break;
            case 'callable': $isType  = is_callable($item); break;
            case 'null'    : $isType  = is_null($item);     break;
            case 'empty'   : $isType  = empty($item);        break;
            case 'bool-true'  : $isType  = $item === true;    break;
            case 'bool-false' : $isType  = $item === false;    break;
            case 'non-empty-string': 
                $isType = ! empty($item) && is_string($item); 
                break;
            default        : 
                $isType  = $item instanceof $type;
        }

        return $isType;
    }

    /**
     * @param   string    $key
     * @param   type    $type    name of class to test for
     * @return  bool
     */
    public function exitsAsObject($key, $type)
    {
        if (! $this->exists($key) || ! is_string($type)) {
            return false;
        }

        return $this->items[$key] instanceof $type;
    }

    /**
     * Return the entire dataset
     * 
     * @return  array
     */
    public function getAll()
    {
        return $this->items;
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
}

