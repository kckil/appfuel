<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

use InvalidArgumentException;

/**
 * Wraps the openlog, syslog, and closelog calls used when logging an appfuel
 * or application specific message
 */
class Logger implements LoggerInterface
{
    /**
     * @var LogAdapterInterface
     */
    protected $adapter = null;

    /**
     * @param   LogAdapterInterface $adapter
     * @return  Logger
     */
    public function __construct(LogAdapterInterface $adapter = null)
    {
        if (null === $adapter) {
            $identity = 'appfuel';
            if (defined('AF_APP_KEY')) {
                $identity = AF_APP_KEY;
            }
            $adapter = new SysLogAdapter($identity);
        }
        $this->setAdapter($adapter);
    }

    /**
     * @return  LogAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param   LogAdapterInterface $adapter
     * @return  null
     */
    public function setAdapter(LogAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @param   LogEntryInterface   $entry
     * @return  bool
     */
    public function logEntry(LogEntryInterface $entry)
    {
        $adapter = $this->getAdapter();
        if (! $adapter->openLog()) {
            return false;
        }
        
        $result = $adapter->writeEntry($entry);
        
        $adapter->closeLog();
        return $result;
    }

    /**
     * @param   string  $text
     * @param   int     $priority
     * @return  bool
     */
    public function log($text, $priority = LOG_INFO)
    {

        if (! empty($text) && is_string($text)) {
            $entry = new LogEntry($text, new LogPriority($priority));
        }
        else if ($text instanceof LogEntryInterface) {
            $entry = $text;
        }
        else {
            $err  = "first param must be a string or an object that ";
            $err .= "implments Appfuel\Log\LogEntryInterface";
            throw new InvalidArgumentException($err);
        }
        
        return $this->logEntry($entry);
    }
}
