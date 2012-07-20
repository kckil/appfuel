<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

/**
 * A single entry in the log
 */
interface LogEntryInterface
{
    /**
     * @return  string
     */
    public function getTimestamp();

    /**
     * @return  string
     */
    public function getText();

    /**
     * @return  LogPriorityInterface
     */
    public function getPriority();

    /**
     * @return  mixed
     */
    public function getPriorityLevel();

    /**
     * @return  string
     */
    public function getEntry();

    /**
     * @return  string
     */
    public function __toString();

}
