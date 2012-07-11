<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource\Yui;

use InvalidArgumentException,
    Appfuel\Html\Resource\FileStackInterface;

/**
 * Adds sorting based on yui3 after property
 */
interface Yui3FileStackInterface extends FileStackInterface
{
    /**
     * @param    string    $type
     * @param    string    $file
     * @param    string    $afterFile
     * @return    FileStack
     */
    public function addAfter($type, $file, $afterFile);

    /**
     * @param    string    $type
     * @return    array
     */
    public function getAfter($type);

    /**
     * @return    Yui3FileStack
     */
    public function sortByPriority();

    /**
     * @param    string    $type    
     * @return    Yui3FileStack
     */
    public function resolveAfter($type);
}
