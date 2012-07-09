<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Console;

use DomainException,    
    InvalidArgumentException;

/**
 * Holds all the input for a given request to the application
 */
class ConsoleInput implements ConsoleInputInterface
{
    /**
     * Name of the command that was issued on the command line
     * @var string
     */
    protected $cmd = null;

    /**
     * List of command line arguments
     * @var array
     */
    protected $args = array();
    
    /**
     * list of short options
     * @var array
     */
    protected $shortOptions = array();

    /**
     * List of long options
     * @var array
     */
    protected $longOptions = array();

    /**
     * @param   array  $data       
     * @return  ConsoleInput
     */
    public function __construct(array $data)
    {
        if (isset($data['cmd'])) {
            $this->setCmd($data['cmd']);
        }

        if (isset($data['args'])) {
            $this->setArgs($data['args']);
        }

        if (isset($data['short'])) {
            $this->setShortOptions($data['short']);
        }

        if (isset($data['long'])) {
            $this->setLongOptions($data['long']);
        }
    }

    /**
     * Used only with command line input. Gets the command name that was used
     * on the commandline
     *
     * @return    string | false
     */
    public function getCmd($isRealPath = false)
    {
        $cmd = $this->cmd;
        if (! empty($cmd) && true === $isRealPath) {
            $cmd = realpath($cmd);
        }

        return $cmd;
    }
  
    /**
     * @return  bool
     */ 
    public function isArgs()
    {
        return ! empty($this->args);
    }
 
    /**
     * @return  array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param   numeric $index
     * @param   mixed   $default
     * @return  mixed
     */
    public function getArg($index, $default = null)
    {
        if (! is_numeric($index) || ! isset($this->args[$index])) {
            return $default;
        }

        return $this->args[$index];
    }

    /**
     * @return  array
     */
    public function getShortOptions()
    {
        return $this->shortOptions;
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isShortOptionFlag($key)
    {
        if (! is_string($key) ||
            ! isset($this->shortOptions[$key]) ||
            true !== $this->shortOptions[$key]) {
            return false;
        }

        return true;
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isShortOption($key)
    {
        if (!is_string($key) || !array_key_exists($key, $this->shortOptions)) {
            return false;
        }

        return true;
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getShortOption($key, $default = null)
    {
        if (! $this->isShortOption($key)) {
            return $default;
        }

        return $this->shortOptions[$key];
    }

    /**
     * @return array
     */
    public function getLongOptions()
    {
        return $this->longOptions;
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isLongOptionFlag($key)
    {
        if (! is_string($key) ||
            ! isset($this->longOptions[$key]) || 
            true !== $this->longOptions[$key]) {
            return false;
        }

        return true;
    }

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isLongOption($key)
    {
        if (! is_string($key) || ! array_key_exists($key, $this->longOptions)) {
            return false;
        }

        return true;
    }

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getLongOption($key, $default = null)
    {
        if (! $this->isLongOption($key)) {
            return $default;
        }

        return $this->longOptions[$key];
    }

    /**
     * @param   string  $short
     * @param   string  $long
     * @return  bool
     */
    public function isOptionFlag($long = null, $short = null)
    {
        if (null !== $long && $this->isLongOptionFlag($long)) {
            return true;
        }

        if (null !== $short && $this->isShortOptionFlag($short)) {
            return true;
        }

        return false;
    }

    /**
     * @param   string  $short
     * @param   string  $long
     * @return  bool
     */
    public function isOption($long = null, $short = null)
    {
        if (null !== $long && $this->isLongOption($long)) {
            return true;
        }

        if (null !== $short && $this->isShortOption($short)) {
            return true;
        }

        return false;
    }

    /**
     * @param   string  $short
     * @param   string  $long
     * @return  bool
     */
    public function getOption($long = null, $short = null, $default = null)
    {
        if (null !== $long && $this->isLongOption($long)) {
            return $this->getLongOption($long);
        }

        if (null !== $short && $this->isShortOption($short)) {
            return $this->getShortOption($short);
        }

        return $default;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setCmd($name)
    {
        if (! is_string($name) || empty($name)) {
            $err = "name of the cmd must be a non empty string";
            throw new InvalidArgumentException($err);
        }

        $this->cmd = $name;
    }

    /**
     * @param   array   $opts
     * @return  null
     */
    protected function setShortOptions(array $opts)
    {
        foreach ($opts as $key => $value) {
            if (! is_string($key) || empty($key)) {
                $err = "invalid short option: key must be a non empty string";
                throw new DomainException($err);
            }
        }
        $this->shortOptions = $opts;
    }

    /**
     * @param   array   $opts
     * @return  null
     */
    protected function setLongOptions(array $opts)
    {
        foreach ($opts as $key => $value) {
            if (! is_string($key) || empty($key)) {
                $err = "invalid long option: key must be a non empty string";
                throw new DomainException($err);
            }
        }
        $this->longOptions = $opts;
    }

    /**
     * @param   array   $args
     * @return  null
     */
    protected function setArgs(array $args)
    {
        foreach ($args as $index => $value) {
            if (! is_numeric($index)) {
                $err = "cli args must be stored with numeric indexes";
                throw new DomainException($err);
            }
        }

        $this->args = $args;
    }
}
