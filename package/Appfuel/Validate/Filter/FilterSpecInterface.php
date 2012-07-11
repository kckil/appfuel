<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Validate\Filter;

/**
 * Value object used to hold information about a filter
 */
interface FilterSpecInterface
{
    /**
     * @return  string
     */
    public function getName();

    /**
     * @return  array
     */
    public function getOptions();

    /**
     * @return  string
     */
    public function getError();
}
