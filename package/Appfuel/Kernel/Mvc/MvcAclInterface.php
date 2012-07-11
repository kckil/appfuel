<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Mvc;

/**
 * Holds a list of acl codes which are checked against route access codes 
 * by the dispatcher
 */
interface MvcAclInterface
{
    /**
     * @return    array
     */
    public function getCodes();

    /**
     * @param   array   $codes
     * @return  MvcAclInterface
     */
    public function setCodes(array $codes);
 
    /**
     * @param   array   $codes
     * @return  MvcAclInterface
     */
    public function loadCodes(array $codes);
       
    /**
     * @param   string  $code
     * @return  MvcAclInterface
     */
    public function addCode($code);

    /**
     * @return  MvcAclInterface
     */
    public function clearCodes();

    /**
     * @param   string  $code
     * @return  bool
     */
    public function isCode($code);
}
