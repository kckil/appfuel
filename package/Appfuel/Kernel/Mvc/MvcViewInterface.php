<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Mvc;

use InvalidArgumentException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Mvc\MvcViewInterface;

/**
 * Holds the content string and data assignments used to contstruct the view.
 * A View Compositor will use a this to compose the view based on the data
 * and format then assign the result back to its content.
 */
interface MvcViewInterface extends ArrayDataInterface
{
    /**
     * @return    string
     */
    public function getFormat();

    /**
     * @param   string $format
     * @return  AppView
     */
    public function setFormat($format);

    /**
     * @param    mixed    $view
     * @return    bool
     */
    public function isValid($view);

    /**
     * @return    bool
     */
    public function isEmpty();

    /**
     * @return    
     */
    public function getContent();

    /**
     * @param   mixed   $view
     * @param   string  $action
     * @return  AppView
     */
    public function setContent($view, $action = 'replace');

    /**
     * @return  string
     */
    public function __toString();
}
