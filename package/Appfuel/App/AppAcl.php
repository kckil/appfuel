<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use DomainException,
    InvalidArgumentException,
    Appfuel\Kernel\Mvc\MvcAclInterface;

/**
 * Holds a list of acl codes which are checked against route access codes 
 * by the dispatcher
 */
class AppAcl implements MvcAclInterface
{
    /**
     * List of acl roles for this context. The dispatcher asks the mvc action
     * if this context will be allowed for processing based on these codes.
     * @var    array
     */
    protected $codes = array();

    /**
     * @return    array
     */
    public function getCodes()
    {
        return $this->codes;
    }

    /**
     * @param   array   $codes
     * @return  AppAcl
     */
    public function setCodes(array $codes)
    {
        $this->clear();
        $this->loadCodes($codes);
        return $this;
    }

    /**
     * @param   array $codes
     * @return  AppAcl
     */
    public function loadCodes(array $codes)
    {
        foreach ($codes as $code) {
            $this->addCode($code);
        }

        return $this;
    }

    /**
     * @param   string    $code
     * @return  AppAcl
     */
    public function addCode($code)
    {
        if (! is_string($code)) {
            throw new InvalidArgumentException("acl code must be a string");
        }
    
        if ($this->isAclCode($code)) {
            return $this;    
        }

        $this->codes[] = $code;
        return $this;
    }

    /**
     * @param    string    $code
     * @return    bool
     */
    public function isCode($code)
    {
        if (! is_string($code) || ! in_array($code, $this->codes, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return  AppAcl
     */
    public function clearCodes()
    {
        $this->codes = array();
        return $this;
    }
}
