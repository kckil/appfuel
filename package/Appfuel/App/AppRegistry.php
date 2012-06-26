<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use Appfuel\DataStructure\ArrayData;
    
/**
 * Decouples settings from the files they are located in. 
 */
class AppRegistry
{
    /**
     * Holds a list of key value pairs
     * @var ArrayData
     */
    static protected $data = null;

    /**
     * @return  array
     */
    static public function getAll()
    {
        return self::getData()->getAll();
    }

    /**
     * @param   $data
     * return   null
     */
    static public function setAll(array $data)
    {
        self::getData()->setAll($data);
    }

    /**
     * @param   $data
     * return   null
     */
    static public function load(array $data)
    {
        self::getData()->load($data);
    }

    /**
     * @param   string  $key
     * @param   mixed   $value
     * @return  null
     */
    static public function add($key, $value)
    {
        self::getData()->add($key, $value);
    } 

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed   
     */
    static public function get($key, $default = null)
    {
        return self::getData()->get($key, $default);
    }

    /**
     * @param   string  $key
     * @param   string  $type
     * @param   mixed   $default
     * @return  mixed
     */
    static public function getWhen($key, $type, $default = null)
    {
        return self::getData()->getWhen($key, $type, $default);
    }

    /**
     * @param   array   $keys
     * @param   bool    $isArray
     * @return  ArrayData | array 
     */
    static public function collect(array $keys, $isArray = true)
    {
        return  self::getData()->collect($keys, $isArray);
    }

    /**
     * @param   array   $keys
     * @param   string  $type
     * @param   bool    $isArray
     * @return  ArrayData | array
     */
    static public function collectWhen(array $keys, $type, $isArray = true)
    {
        return self::getData()->collectWhen($keys, $type, $isArray);
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    static public function exists($key)
    {
        return  self::getData()->exists($key);
    }

    /**
     * @param   string  $key
     * @param   string  $type
     * @return  bool
     */
    static public function existAs($key, $type)
    {
        return self::getData()->existAs($key, $type);
    }

    /**
     * @param   string  $key 
     * @return  null
     */
    static public function clear($key = null)
    {
        self::getData()->clear($key);
    }

    /**
     * @return  ArrayData
     */
    static protected function getData()
    {
        if (null === self::$data) {
            self::$config = new ArrayData();
        }

        return self::$config;
    }
}
