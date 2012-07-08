<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\View;

use RunTimeException,
    InvalidArgumentException,
    Appfuel\Html\Resource\PkgName,
    Appfuel\Html\Resource\PkgNameInterface;

/**
 * Deprecated: should not be used. Please use
 * Appfuel\Kernel\Mvc\MvcViewInterface
 */
interface TemplateInterface extends ViewInterface
{
    /**
     * Relative file path to template file
     * @return    null
     */
    public function getFile();

    /**
     * @param   string    $file
     * @return  ViewTemplate
     */
    public function setFile($file);

    /**
     * @return  PkgNameInterface
     */
    public function getViewPkgName();

    /**
     * @param   PkgNameInterface $name
     * @return  FileTemplate
     */
    public function setViewPkgName(PkgNameInterface $name);

    /**
     * @param   string    $name
     * @param   string    $defaultVendor
     * @return  PkgName
     */
    public function createViewPkgName($name, $defaultVendor = null);

    /**
     * @return  bool
     */
    public function isViewPackage();

    /**
     * @param   string    $name 
     * @param   string    $defaultVendor
     * @return  FileTemplate
     */
    public function setViewPackage($name, $defaultVendor = null);
}
