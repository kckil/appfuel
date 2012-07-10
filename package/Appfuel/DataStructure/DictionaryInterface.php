<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\DataStructure;

use Countable;

/**
 * Deprecated: use ArrayDataInterface instead
 */
interface DictionaryInterface extends Countable
{
    /**
     * @param   string  $key
     * @param   mixed   $value    
     * @return  DictionaryInterface
     */    
    public function add($key, $value);

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($key, $default = NULL);

    /**
     * @return  array
     */
    public function getAll();

    /**
     * @param   array    $data
     * @return  DictionaryInterface
     */
    public function load(array $data);
}
