<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use DomainException,    
    InvalidArgumentException;

interface ConsoleInputInterface
{

    /**
     * Used only with command line input. Gets the command name that was used
     * on the commandline
     *
     * @return    string | false
     */
    public function getCmd($isRealPath = false);
  
    /**
     * @return  bool
     */ 
    public function isArgs();
 
    /**
     * @return  array
     */
    public function getArgs();

    /**
     * @param   numeric $index
     * @param   mixed   $default
     * @return  mixed
     */
    public function getArg($index, $default = null);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isShortOptionFlag($key);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isShortOption($key);

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getShortOption($key, $default = null);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isLongOptionFlag($key);

    /**
     * @param   string  $key
     * @return  bool
     */
    public function isLongOption($key);

    /**
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed
     */
    public function getLongOption($key, $default = null);

    /**
     * @param   string  $short
     * @param   string  $long
     * @return  bool
     */
    public function isOption($long = null, $short = null);

    /**
     * @param   string  $short
     * @param   string  $long
     * @return  bool
     */
    public function getOption($long = null, $short = null, $default = null);
}
