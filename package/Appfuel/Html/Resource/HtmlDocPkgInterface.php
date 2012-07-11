<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use DomainException,
    InvalidArgumentException;


interface HtmlDocPkgInterface extends PagePkgInterface
{
    /**
     * @return  string
     */
    public function getHtmlConfig();
}
