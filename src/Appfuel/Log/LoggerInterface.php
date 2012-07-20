<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

/**
 * The log adapter is used to by the logger to actually log the messages.
 */
interface LoggerInterface
{
    /**
     * @return  LogAdapterInterface
     */
    public function getAdapter();

    /**
     * @param   LogAdapterInterface $adapter
     * @return  LoggerInterface
     */
    public function setAdapter(LogAdapterInterface $adapter);

    /**
     * @param   LogEntryInterface   $entry
     * @return  bool
     */
    public function logEntry(LogEntryInterface $entry);

    /**
     * @param   string  $text
     * @param   int     $priority
     * @return  bool
     */
    public function log($text, $priority = LOG_INFO);
}
