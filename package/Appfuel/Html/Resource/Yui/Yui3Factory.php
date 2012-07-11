<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource\Yui;

use DomainException,
    Appfuel\Html\Resource\VendorInterface,
    Appfuel\Html\Resource\ResourceFactory,
    Appfuel\Html\Resource\ResourceFactoryInterface;

/**
 * Creates objects needed to traverse appfuel resource dependencies
 */
class Yui3Factory extends ResourceFactory implements ResourceFactoryInterface
{
    /**
     * @param    string    $vendor 
     * @return    ResourceAdapterInterface
     */
    public function createResourceAdapter()
    {
        return new Yui3Adapter();
    }

    /**
     * @return    FileStackInterface
     */
    public function createFileStack()
    {
        return new Yui3FileStack();
    }

    /**
     * @param    string    $name
     * @param    VendorInterface $vendor
     * @return    ResourceLayerInterface
     */
    public function createLayer($name, VendorInterface $vendor)
    {
        return new Yui3Layer($name, $vendor);
    }

    /**
     * @param    array    $data
     * @return    AppfuelManifestInterface
     */
    public function createPkg(array $data, $vendor = null)
    {
        return new Yui3Pkg($data);
    }
}
