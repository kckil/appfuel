<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

/**
 * Value object used to describe the vendor information
 */
interface VendorInterface
{
    /**
     * @return    string
     */
    public function getVendorName();

    /**
     * @return    string
     */
    public function getPackagePath();

    /**
     * @return  string
     */
    public function getVersion();

    /**
     * @return    string
     */
    public function getPackageTreePath();

}
