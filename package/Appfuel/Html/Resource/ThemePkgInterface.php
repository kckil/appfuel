<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

interface ThemePkgInterface extends PkgInterface
{
    /**
     * @return    bool
     */
    public function isCssFiles();

    /**
     * @param    string    $path    
     * @return    array
     */
    public function getCssFiles($path = null);

    /**
     * @return    bool
     */
    public function isAssetFiles();

    /**
     * @param    string    $path
     * @return    array
     */
    public function getAssetFiles($path = null);

}
