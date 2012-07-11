<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use DomainException,
    InvalidArgumentException;

/**
 */
interface ResourceLayerInterface 
{
    /**
     * @return    string
     */
    public function getLayerName();

    /**
     * @param    string    $name
     * @return    ResourceLayer
     */
    public function setLayerName($name);

    /**
     * @return    VendorInterface
     */
    public function getVendor();

    /**
     * @param    string    $name
     * @return    ResourceLayer
     */
    public function setVendor(VendorInterface $vendor);

    /**
     * @return    string
     */
    public function getFilename();

    /**
     * @param    string    $name
     * @return    Yui3Layer
     */
    public function setFilename($name);

    /**
     * @return    bool
     */
    public function isFilename();
}
