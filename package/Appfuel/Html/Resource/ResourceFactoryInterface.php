<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

/**
 * Creates objects needed to traverse appfuel resource dependencies
 */
interface ResourceFactoryInterface
{
    /**
     * @param    string    $vendor 
     * @return    ResourceAdapterInterface
     */
    public function createResourceAdapter();

    /**
     * @return    FileStackInterface
     */
    public function createFileStack();

    /**
     * @param    string    $name
     * @param    VendorInterface $vendor
     * @return    ResourceLayerInterface
     */
    public function createLayer($name, VendorInterface $vendor);

    /**
     * @param    array    $data
     * @return    VendorInterface
     */
    public function createVendor(array $data);

    /**
     * @param    array    $data
     * @return    AppfuelManifestInterface
     */
    public function createPkg(array $data, $vendor = null);
}
