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
interface PagePkgInterface extends PkgInterface
{
    /**
     * @return    string
     */
    public function getHtmlDocName();

    /**
     * @return    string
     */
    public function getMarkupFile();

    /**
     * @return    string
     */
    public function getJsInitFile();

    /**
     * @return    bool
     */
    public function isJsInitFile();

    /**
     * @return    string
     */
    public function getLayers();
}
