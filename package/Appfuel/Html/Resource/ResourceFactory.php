<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use DomainException;

/**
 * Creates objects needed to traverse appfuel resource dependencies
 */
class ResourceFactory implements ResourceFactoryInterface
{
    /**
     * @param    string    $vendor 
     * @return    ResourceAdapterInterface
     */
    public function createResourceAdapter()
    {
        return new AppfuelAdapter();
    }

    /**
     * @return    FileStackInterface
     */
    public function createFileStack()
    {
        return new FileStack();
    }

    /**
     * @param    array    $data
     * @return    VendorInterface
     */
    public function createVendor(array $data)
    {
        return new Vendor($data);
    }

    /**
     * @param    string    $name
     * @param    VendorInterface $vendor
     * @return    ResourceLayerInterface
     */
    public function createLayer($name, VendorInterface $vendor)
    {
        return new AppfuelLayer($name, $vendor);
    }

    /**
     * @param    array    $data
     * @return    AppfuelManifestInterface
     */
    public function createPkg(array $data, $vendor = null)
    {
        if (! isset($data['type'])) {
            $err = 'manifest must have a type property';
            throw new DomainException($err);
        }
        $type = $data['type'];
        
        switch ($type) {
            case 'pkg':
                $pkg = new Pkg($data, $vendor);
                break;
            case 'view':
                $pkg = new ViewPkg($data, $vendor);
                break;
            case 'page':
                $pkg = new PagePkg($data, $vendor);
                break;
            case 'htmldoc':
                $pkg = new HtmlDocPkg($data, $vendor);
                break;
            case 'theme':
                $pkg = new ThemePkg($data, $vendor);
                break;
            default:
                $err = "invalid pkg: no pkg strategy for -($type)";
                throw new DomainException($err);
        }

        return $pkg;
    }

    /**
     * @param    string    $name
     * @param    string    $vendor
     * @return    PkgName
     */
    public function createPkgName($name, $vendor = null)
    {
        return new PkgName($name, $vendor);
    }
}
