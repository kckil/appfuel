<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Mvc;

use Appfuel\DataStructure\DictionaryInterface;

/**
 * The app context holds the input (get, post, argv etc..), handles errors and 
 * is a dictionary that can hold hold key value pairs allowing custom objects
 * specific to the application to be added without having to extends the 
 * context. The context is passed into each intercepting filter and then into
 * the action controllers process method.
 */
interface MvcContextInterface extends DictionaryInterface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return  string
     */
    public function getRouteKey();

    /**
     * @return  ViewTemplateInterface
     */
    public function getView();

    /**
     * @return  array
     */
    public function getAcl();

    /**
     * @param   string  $code
     * @return  AppContext
     */
    public function setAcl($codes);
    
    /**
     * @return  int
     */
    public function getExitCode();
    
    /**
     * @param   int $code
     * @return  AppContext
     */
    public function setExitCode($code);

    /**
     * @return  ContextInputInterface
     */
    public function getInput();

    /**
     * @param   mixed   $input
     * @return  bool
     */
    public function isValidHttpInput($input);

    /**
     * @param   mixed   $input
     * @return  bool
     */
    public function isValidConsoleInput($input);

    /**
     * @param   string  $key
     * @param   MvcContextInterface $context
     * @return  MvcContextInterface
     */
    public function cloneContext($key, MvcContextInterface $context);

    /**
     * @param   MvcContextInterface $context
     * @param   bool    $isReplaceInput
     * @return  MvcContextInterface
     */
    public function merge(MvcContextInterface $context, $isReplaceInput=false);
}
