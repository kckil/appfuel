<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use Exception,
    DomainException;

/**
 * Maps the input method (http[get,post,put,delete] or cli)
 * to a concrete MvcAction.
 */
class RouteAccessSpec implements RouteAccessSpecInterface
{
    /**                                                                          
     * Flag used to determine if this route is public and reqiures no acl check  
     * @var  bool                                                                
     */                                                                          
    protected $isPublic = false;                                                 
                                                                                 
    /**                                                                          
     * Flag used to detemine if the controller used by this route is internal.   
     * Internal routes can not be executed by the front controller and thus      
     * inaccessible from the outside                                             
     * @var bool                                                                 
     */                                                                          
    protected $isInternal = false;                                               
                                                                                 
    /** 
     * Used when an internal action is trusting that the calling action checked
     * for acl access.                                                                        
     * @var bool                                                                 
     */                                                                          
    protected $isIgnoreAcl = false;                                              
                                                                                 
    /**                                                                          
     * Flag used to determine if acl access is mapped foreach method. Used in          
     * restful calls, to allow for different acl codes with get, post, delete, 
     * and put (http methods are not strictly enforced)     
     * @var bool                          
     */                                                                          
    protected $isAclForEachMethod = false;

    /**
     * Can be a list of acl codes or a map of http method to acl codes.
     * @var array
     */
    protected $acl = array();

    /**
     * @param   array   $spec
     * @return  RouteAction
     */
    public function __construct(array $spec)
    {
        if (isset($spec['is-public']) && true === $spec['is-public']) {
            $this->isPublic = true;
        }

        if (isset($spec['is-internal']) && true === $spec['is-internal']) {
            $this->isInternal = true;
        }

        if (isset($spec['is-ignore-acl']) && true === $spec['is-ignore-acl']) { 
            $this->isIgnoreAcl = true;
        }

        if (isset($spec['acl'])) {
            $this->setAcl($spec['acl']);    
        }
    }

    /**
     * @return  bool
     */
    public function isPublicAccess()
    {
        return $this->isPublic;
    }

    /**
     * @return  bool
     */
    public function isInternalOnlyAccess()
    {
        return $this->isInternal;
    }

    /**
     * @return bool
     */
    public function isAclAccessIgnored()
    {
        return $this->isIgnoreAcl;
    }

    /**
     * @return  bool
     */
    public function isAclForEachMethod()
    {
        return $this->isAclForEachMethod;
    }

    /**
     * @param   string  $code
     * @return  bool
     */
    public function isAccessAllowed($codes, $method = null)
    {
        if ($this->isPublicAccess() || $this->isAclAccessIgnored()) {
            return true;
        }

        if (is_string($codes)) {
            $codes = array($codes);
        }
        else if (! is_array($codes)) {
            return false;
        }

        $compare = array();
        foreach ($codes as $code) {
            if (is_string($code) && ! empty($code)) {
                $compare[] = $code;
            }
        }

        $acl = $this->getAcl();
        if ($this->isAclForEachMethod()) {
            if (! is_string($method) || 
                ! isset($acl[$method]) ||
                ! is_array($acl[$method])) {
                return false;
            }
            $acl = $acl[$method];
        }

        $result = array_intersect($acl, $compare);

        if (empty($result)) {
            return false;
        }

        return true;
    }

    /**
     * @param   array
     * @return  null
     */
    protected function setAcl(array $map)
    {
        if ($map !== array_values($map)) {
            $this->validateMappedAclCodes($map);
            $this->isAclForEachMethod = true;
        }
        else {
            $this->validateAclCodes($map);
            $this->isAclForEachMethod = false;
        }

        $this->acl = $map;
    }

    /**
     * @return  array
     */
    protected function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param   array    $codes
     * @return  bool
     */
    protected function validateAclCodes(array $codes)
    {
        foreach ($codes as $code) {
            if (! is_string($code) || empty($code)) {
                $err = "all acl codes must be non empty strings";
                throw new DomainException($err);
            }
        }

        return true;
    }

    /**
     * @param   array   $codes
     * @return  bool
     */
    protected function validateMappedAclCodes(array $map)
    {
        foreach ($map as $method => $codes) {
            if (! is_string($method) || empty($method)) {
                $err  = "the method acl codes are mapped to must be a ";
                $err .= "non empty string";
                throw new DomainException($err);
            }

            if (! is_array($codes)) {
                $err = "list of codes for -($method) must be an array";
                throw new DomainException($err);
            }
            foreach ($codes as $code) {
                if (! is_string($code) || empty($code)) {
                    $err  = "acl code for -($method) must be a non empty ";
                    $err .= "string";
                    throw new DomainException($err);
                }
            }
        }

        return true;
    }
}
