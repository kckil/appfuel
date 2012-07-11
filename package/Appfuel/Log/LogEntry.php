<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Log;

use RunTimeException;

/**
 * Value object that represents a single log entry
 */
class LogEntry implements LogEntryInterface
{
    /**
     * Number or text used to represent the error
     * @var int
     */
    protected $priority = null;

    /**
     * Timestamp of when the entry was created
     * @var string
     */
    protected $timestamp = null;

    /**
     * Text used for the log entry
     * @var string
     */
    protected $entryText = null;

    /**
     * @param   string    $text 
     * @param   scalar    $priority
     * @return  LogEntry 
     */
    public function __construct($text, $priority = null)
    {
        if (null === $priority || is_int($priority)) {
            $priority = new LogPriority($priority);
        }

        $this->setPriority($priority);
        $this->setTimestamp();
        $this->setText($text);
    }

    /**
     * @return  string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return  string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return  int
     */
    public function getPriority()
    {
        return $this->priority;
    }
    
    /**
     * @return  mixed
     */
    public function getPriorityLevel()
    {
        return $this->priority
                    ->getLevel();
    }

    /**
     * @return  string
     */
    public function getEntry()
    {
        $date = date("d-m-Y H:i:s", $this->getTimestamp());
        return "[$date] {$this->getText()}";
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        return $this->getEntry();
    }

    /**
     * @return  null
     */
    protected function setTimestamp()
    {
        $this->timestamp = strtotime('now');
    }

    /**
     * @throws  RunTimeException
     * @param   string | object 
     * @return  null
     */
    protected function setText($str)
    {
        $text = '';
        if (! empty($str) && is_string($str) && ($str = trim($str))) {
            $text = $str;
        }
        else if (is_scalar($str) || 
                 is_object($str) && is_callable(array($str, '__toString'))) {
            $text =(string) $str;
        }
        else {
            $err = "entry must be text or implement __toString";
            throw new RunTimeException($err);
        }
        
        $this->text = $text;
    }

    /**
     * @param   int     $level
     * @return  null
     */
    protected function setPriority(LogPriorityInterface $level)
    {
        $this->priority = $level;
    }
}
