<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

/**
 * Allows control over which tasks are applied during application startup.
 * Note: this does not include control over framework tasks. Tasks may be
 * added or excluded. All tasks or just tasks defined in the config files
 * may be disabled.
 */
interface RouteStartupSpecInterface
{
    /**
     * @param   array   $spec
     * @return  RouteStartupInterface
     */
    public function __construct(array $spec);

    /**
     * @return  bool
     */
    public function isPrependStartupTasks();

    /**
     * @return  bool
     */
    public function isIgnoreConfigStartupTasks();

    /**
     * @return  bool
     */
    public function isStartupDisabled();

    /**
     * @return  bool
     */
    public function isStartupTasks();

    /**
     * @return  array
     */
    public function getStartupTasks();

    /**
     * @return  bool
     */
    public function isExcludedStartupTasks();

    /**
     * @return  array
     */
    public function getExcludedStartupTasks();
}
