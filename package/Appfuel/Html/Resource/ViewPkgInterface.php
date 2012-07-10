<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

use InvalidArgumentException;

/**
 * A view is a package that has a markup file. The markup file
 * contains the html structure of the view.
 */
interface ViewPkgInterface extends PkgInterface
{
    /**
     * @return    string
     */
    public function getMarkupFile($path = null);

    /**
     * @return    string
     */
    public function isJsView();
}
