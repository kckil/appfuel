<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Html\Resource;

/**
 * Write a resource tree to disk
 */
interface ResourceTreeWriterInterface
{
    /**
     * @param    string    $path    
     * @param    bool    $isBasePath
     * @return    TreeBuilder
     */
    public function writeTree(array $tree, $path = null, $isBasePath = true);

    /**
     * @return    string
     */
    public function getDefaultTreeFile();
}
