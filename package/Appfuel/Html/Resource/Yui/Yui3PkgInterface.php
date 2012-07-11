<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource\Yui;

interface Yui3PkgInterface
{
    /**
     * @return    string
     */
    public function getName();

    /**
     * @return    array
     */
    public function getRequire();

    /**
     * @return    bool
     */
    public function isRequire();

    /**
     * @return    array
     */
    public function getUse();

    /**
     * @return    bool
     */
    public function isUse();

    /**
     * @return array
     */
    public function getAfter();

    /**
     * @return    bool
     */
    public function isAfter();

    /**
     * @return    array
     */
    public function getLang();

    /**
     * @return    bool
     */
    public function isLang();

    /**
     * @return    bool
     */
    public function isSkinnable();

    /**
     * @return    bool
     */
    public function isCss();
}
