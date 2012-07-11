<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use DomainException,
    InvalidArgumentException,
    Appfuel\Html\Resource\Yui\Yui3ResourceAdapter;

/**
 * Pulls info about vendors, packages, and layers out of the resource tree
 */
class ResourceTree
{
    /**
     * @var array
     */
    static protected $tree = array();

    /**
     * list of vendor objects
     * @var array
     */
    static protected $vendors = array();


    /**
     * @return    array
     */
    static public function getAllVendorKeys()
    {
        return array_keys(self::$tree);
    }

    /**
     * @param    string    $name
     * @return    VendorInterface
     */
    static public function getVendor($name)
    {
        if (! is_string($name) || ! isset(self::$vendors[$name])) {
            return false;
        }

        return self::$vendors[$name];
    }

    /**
     * @param    string    $name
     * @param    VendorInteface    $vendor
     * @return    null
     */
    static public function setVendor($name, VendorInterface $vendor)
    {
        if (! is_string($name) || empty($name)) {
            $err = 'vendor name must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        self::$vendors[$name] = $vendor;
    }

    /**
     * @param    string    $key
     * @return    bool
     */
    static public function isVendorInTree($key)
    {
        if (is_string($key) && isset(self::$tree[$key])) {
            return true;
        }

        return false;
    }

    /**
     * @param    string    $vendor
     * @return    string | false 
     */
    static public function getVersion($vendor)
    {
        if (! self::isVendorInTree($vendor) || 
            ! isset(self::$tree[$vendor]['version'])) {
            return false;
        }

        return self::$tree[$vendor]['version'];
    }

    /**
     * @param    string    $vendor
     * @return    string
     */
    static public function getPath($vendor)
    {
        if (! self::isVendorInTree($vendor) || 
            ! isset(self::$tree[$vendor]['path'])) {
            return false;
        }

        return self::$tree[$vendor]['path'];
    }

    /**
     * @param    string    $vendor
     * @param    string    $key
     * @return    bool
     */
    static public function isLayer($vendor, $name)
    {
        if (! is_string($name) || 
            ! self::isVendorInTree($vendor) || 
            ! isset(self::$tree[$vendor]['layers']) ||
            ! isset(self::$tree[$vendor]['layers'][$name])) {
            return false;
        }

        return true;
    }

    /**
     * @param    string    $vendor
     * @return    array | false
     */
    static public function getAllPageNames($vendor)
    {
        if (! self::isVendorInTree($vendor)) {
            return false;
        }

        if (! isset(self::$tree[$vendor]['list']['page'])) {
            return false;
        }

        return array_keys(self::$tree[$vendor]['list']['page']);
    }

    /**
     * @param    string    $vendor
     * @return    array | false
     */
    static public function getAllLayerNames($vendor)
    {
        if (! self::isVendorInTree($vendor)) {
            return false;
        }

        if (! isset(self::$tree[$vendor]['layers'])) {
            return false;
        }

        return array_keys(self::$tree[$vendor]['layers']);
    }

    /**
     * @param    string    $vendor
     * @param    string    $name
     * @return    array | false
     */
    static public function getLayer($vendor, $name)
    {
        if (! self::isLayer($vendor, $name)) {
            return false;
        }

        $result = self::$tree[$vendor]['layers'][$name];
        
        /* since this array will be used in a value object we will add back
         * the info used to create heirarchical structure
         */
        $result['vendor'] = $vendor;
        $result['name']   = $name;
        
        return $result;
    }

    static public function setLayer($vendor, $name, $layer)
    {
        if (! self::isVendorInTree($vendor)) {
            $err = 'can not set layer for vendor that does not exist';
            throw new DomainException($err);
        }

        self::$tree[$vendor]['layers'][$name] = $layer;    
    }

    /**
     * @param    string    $vendor
     * @param    string    $name
     * @return    bool
     */
    static public function isPackage($vendor, $name)
    {
        if (! is_string($name) || 
            ! self::isVendorInTree($vendor) || 
            ! isset(self::$tree[$vendor]['list']) ||
            ! isset(self::$tree[$vendor]['list'][$name])) {
            return false;
        }

        return true;
    }

    /**
     * @param    string    $vendor
     * @param    string    $name
     * @return    array | false
     */
    static public function getPackage($vendor, $name)
    {
        if (! self::isPackage($vendor, $name)) {
            return false;
        }

        $result = self::$tree[$vendor]['list'][$name];
        if (is_array($result) && ! isset($result['name'])) {
            $result['name'] = $name;
        }
        
        return $result;
    }

    /**
     * @param    string    $vendor
     * @param    string    $name
     * @param    mixed    $pkg
     */
    static public function setPackage($vendor, $name, $pkg)
    {
        if (! self::isPackage($vendor, $name)) {
            $err  = "could not set pkg vendor or pkg not found ";
            $err .= "-($vendor, $name)";
            throw new RunTimeException($err);
        }

        self::$tree[$vendor]['list'][$name] = $pkg;
    }


    /**
     * @param    string    $vendor
     * @param    string    $type
     * @return    bool
     */
    static public function isPackageType($vendor, $type)
    {
        if (! is_string($type) ||
            ! self::isVendorInTree($vendor) ||
            ! isset(self::$tree[$vendor]['list'][$type])) {
            return false;
        }

        return true;
    }

    /**
     * @param    string    $vendor
     * @param    string    $type
     * @param    string    $name
     * @return    array | false
     */
    static public function getPackageByType($vendor, $type, $name)
    {
        if (! self::isPackageType($vendor, $type) ||
            ! isset(self::$tree[$vendor]['list'][$type][$name])) {
            return false;
        }

        return self::$tree[$vendor]['list'][$type][$name];
    }

    /**
     * @param    string    $vendor
     * @param    string    $type
     * @param    string    $name
     * @param    mixed    $type
     * @return    array | false
     */
    static public function setPackageByType($vendor, $type, $name, $pkg)
    {
        if (! self::isPackageType($vendor, $type) ||
            ! isset(self::$tree[$vendor]['list'][$type][$name])) {
            $err  = "could not set pkg vendor, type or pkg not found ";
            $err .= "-($vendor, $type, $name)";
            throw new RunTimeException($err);    
        }

        self::$tree[$vendor]['list'][$type][$name] = $pkg;
    }

    /**
     * @return    bool
     */
    static public function isTree()
    {
        return count(self::$tree) > 0;
    }

    /**
     * @return    array
     */
    static public function getTree()
    {
        return self::$tree;
    }

    /**
     * @param    array    $tree
     * @return    null
     */
    static public function setTree(array $tree)
    {
        self::$tree = $tree;
    }

    /**
     * @return    string
     */
    static public function getSeparator()
    {
        return self::$sep;
    }

    /**
     * @param    string    $char
     * @return    null
     */
    static public function setSeparator($char)
    {
        if (! is_string($char) || empty($char)) {
            $err = "array separator must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        self::$sep = $char;
    }

    /**
     * @param    string    $vendor
     * @return    ResourceAdapterInterface
     */
    static public function createAdapter($vendor)
    {
        if ('yui3' === $vendor) {
            return new Yui3ResourceAdapter();
        }

        return new AppfuelResourceAdapter();
    }
}
