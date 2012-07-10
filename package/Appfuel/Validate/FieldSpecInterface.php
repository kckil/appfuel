<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate;

/**
 * Value object used to determine how a field is validated/filtered 
 */
interface FieldSpecInterface
{
    /**
     * @return  string
     */
    public function getFields();

    /**
     * @return  string
     */
    public function getLocation();

    /**
     * @return  string
     */
    public function getFilterSpec();

    /**
     * @return  string
     */
    public function getFilters();

    /**
     * @return  string
     */
    public function getValidator();
}
