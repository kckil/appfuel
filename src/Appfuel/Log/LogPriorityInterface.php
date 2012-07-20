<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

/**
 * Defines a priority for a given log entry. Should be a value object
 */
interface LogPriorityInterface
{
    /**
     * @return  scalar
     */
    public function getLevel();

    /**
     * @return  string
     */
    public function __toString();
}
