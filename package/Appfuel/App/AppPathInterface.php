<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

/**
 * Allows the app dir structure to change without changing the kernel code.
 */
interface AppPathInterface
{
    /**
     * @param   string  $basePath
     * @return  AppDetail
     */
    public function __construct(array $spec);

    /**
     * @return  bool
     */
    public function isStrict();

    /**
     * @return  AppPathInterface
     */
    public function enableStrictMode();

    /**
     * @return  AppPathInterface
     */
    public function disableStrictMode();

    /**
     * @return  string
     */
    public function getBasePath();

    /**
     * @param   string  $name
     * @return  string | false
     */
    public function getPath($name, $isAbsolute = true);
}
