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
 * A value object used to describe the manifest.json in the pkg directory
 */
class ThemePkg extends Pkg implements ThemePkgInterface
{
    /**
     * Used to validate that the type of package is the expected one
     * @var string
     */
    protected $validType = 'theme';

    /**
     * @return    bool
     */
    public function isRequiredPackages()
    {
        return false;
    }

    /**
     * @return    array
     */
    public function getRequiredPackages()
    {
        return array();
    }
}
