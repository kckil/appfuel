<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

interface RouteViewSpecInterface
{
    /**
     * @param   array   $spec
     * @return  RouteViewSpec
     */
    public function __construct(array $spec);

    /**
     * @return  string
     */
    public function getDefaultFormat();

    /**
     * @return  bool
     */
    public function isViewDisabled();

    /**
     * @return  bool
     */
    public function isManualView();

    /**
     * @return  bool
     */
    public function isViewPackage();

    /**
     * @return  string
     */
    public function getViewPackage();
}
