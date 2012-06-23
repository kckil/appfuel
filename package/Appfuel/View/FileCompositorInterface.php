<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                  
 * See LICENSE file at the project root directory for details.                   
 */
namespace Appfuel\View;

use RunTimeException,
    InvalidArgumentException;

/**
 * The template formatter binds a template file with the formatter object. This
 * means the $this in the template file is this object. This format interface
 * will be used in the .phtml files 
 */
interface FileCompositorInterface
{
    /**
     * Load a list of key/value pairs into template file
     * 
     * @param   array   $data
     * @return  FileCompositorInterface
     */
    public function load(array $data);

    /**
     * Assign a key value pair into the template
     *
     * @throws  InvalidArgumentException    when key is a non empty string
     * @param   string    $key
     * @param   mixed    $value
     * @return  FileCompositor 
     */
    public function assign($key, $value);

    /**
     * Get the value for the given label from scope. If the value does not 
     * exist then return the default parameter. Traverse other arrays with
     * the dot syntax. Example get('user.email.address') will look into 
     * the array user then into array email for key address.
     *
     * @param   string  $label      data label 
     * @param   mixed   $default    value returned used when data not found
     * @return  mixed
     */
    public function get($key, $default = null);

    /**
     * Return all the data in scope
     * 
     * @return array
     */
    public function getAll();

    /**
     * echo the value found at label or default if nothing is found
     * 
     * @param   string  $key        label used to identify value in scope
     * @param   mixed   $default    what to render when the key is not found
     * @param   mixed   $sep        separated used to render an array 
     * @return  null
     */
    public function render($key, $default = '', $sep = ' ');

    /**
     * Render Json
     * Helper function to generate a json encoded string of the contents
     * specified by the key.
     *
     * @param   string  $key
     * @return  null
     */
    public function renderAsJson($key, $default = null);

    /**
     * @param   string  $label
     * @return  bool
     */
    public function exists($key);

    /**
     * @return int
     */
    public function count();

    /**
     * @param   string  $file
     * @param   array   $data
     * @param   bool    $isRender
     * @return  string
     */
    public function import($file, array $data = null, $isRender = false);
}
