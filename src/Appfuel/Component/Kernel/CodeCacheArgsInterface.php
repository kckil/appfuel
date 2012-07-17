<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Component\Kernel;

/**
 * Holds the argument specification for the CodeCacheHandler. This should be
 * a value object although not enforced by the interface.
 */
interface CodeCacheArgsInterface
{
    /**
     * A list of fully qualified namespaces. These are the classes to be stored
     * in the cache file.
     * @return  array
     */
    public function getClasses();

    /**
     * The path to the cache directory. Should be an Absolute path, not 
     * enforced.
     * @return  string
     */
    public function getCacheDir();

    /**
     * Full path to the cache file. This is made up by the following rules
     * 1) when not adaptive the path structure is {dir}{key}{ext}
     * 2) when adpative the path structure {dir}{key}-{hash}{ext}
     * 
     * @return  string
     */
    public function getCacheFilePath();

    /**
     * Full path to the meta cache file. The path structure is {cachefile}.meta
     * where {cachefile} is getCacheFilePath
     *
     * @return string
     */
    public function getCacheMetaFilePath();

    /**
     * Key used to identify this cache file
     * @return  string
     */
    public function getCacheKey();

    /**
     * Flag used to determine if the cache should be reloaded.
     * @return  bool
     */
    public function isAutoReload();
}
