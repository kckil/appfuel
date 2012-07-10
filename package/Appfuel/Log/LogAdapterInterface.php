<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

use RunTimeException;

/**
 * The log adapter is used to by the logger to actually log the messages.
 */
interface LogAdapterInterface
{
    /**
     * @param    LogEntryInterface    $entry
     * @return    bool
     */
    public function writeEntry(LogEntryInterface $entry);

    /**
     * @param   string  $text
     * @param   int     $priority
     * @return  bool
     */
    public function write($text, $priority = LOG_INFO);

    /**
     * @return  bool
     */
    public function openLog();

    /**
     * @return  bool
     */
    public function closeLog();
}
