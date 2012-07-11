<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

interface PkgNameInterface
{
    /**
     * @return  string
     */
    public function getVendor();

    /**
     * @return  array
     */
    public function getName();
}
