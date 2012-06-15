<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * For complete copywrite and license details see the LICENSE file distributed
 * with this source code.
 */
namespace Appfuel\Kernel\Route;

use Exception,
	DomainException;

/**
 * Maps the input method (http[get,post,put,delete] or cli)
 * to a concrete MvcAction.
 */
class RouteActionSpec implements RouteActionSpecInterface
{
	/**
	 * Name of the mvc action class. This is not the qual
	 * @var string
	 */
	protected $name = null;

	/**
	 * Holds a map of http method (not enforced, can be whatever you want)
	 * to mvc action class name
	 * @var array
	 */
	protected $actionMap = array();

	/**
	 * @var string
	 */
	protected $namespace = null;

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
	 * @var	array
	 */
	protected $aclMap = array();

	/**
	 * @param	array	$spec
	 * @return	RouteAction
	 */
	public function __construct(array $spec)
	{
		if (! isset($spec['namespace'])) {
			$err = "mvc action namespace is required but not set";
			throw new DomainException($err);
		}
		$this->setNamespace($spec['namespace']);

		if (! isset($spec['action-name']) && ! isset($spec['map'])) {
			$err  = 'the action name or map must be set in order for the ';
			$err .= ' dispatcher to be able to create it';
			throw new DomainException($err);
		}

		if (isset($spec['map'])) {
			$this->setActionMap($spec['map']);
		}
		else if (isset($spec['action-name'])) {
			$this->setName($spec['action-name']);
		}
		else {
			$err  = 'key -(action-map|action-name) must be non empty string ';
			$err .= 'or an array of method=>actionName mappings';
			throw new DomainException($err);
		}

		if (isset($spec['is-public']) && true === $spec['is-public']) {
			$this->isPublic = true;
		}

		if (isset($spec['is-internal']) && true === $spec['is-internal']) {
			$this->isInternal = true;
		}

		if (isset($spec['is-ignore-acl']) && true === $spec['is-ignore-acl']) { 
			$this->isIgnoreAcl = true;
		}

		if (isset($spec['acl-access'])) {
			$this->setAclMap($spec['acl-access']);	
		}
	}

	/**
	 * @param	string	$method 
	 * @return	string | false
	 */
	public function findAction($method = null, $isQualified = true)
	{
		if ($this->isMapEmpty()) {
			$name = $this->getName();
		}
		else {
			$name = $this->getNameInMap($method);
		}

		if (true === $isQualified && ! empty($name)) {
			$name = "{$this->getNamespace()}\\$name";
		}

		return $name;
	}

	/**
	 * @return	string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param	string	$method
	 * @return	MvcActionInterface
	 */
	public function createAction($method = null)
	{
		$class = $this->findAction($method);
		if (empty($class)) {
			$err = "mvc action class has not been mapped: class not found";
			throw new DomainException($err);
		}

        try {                                                                    
            $action = new $class();                                              
        } catch (Exception $e) {                                                 
            $msg = $e->getMessage();                                             
            $err = "action spec could not create -($class, $method): $msg";       
            throw new DomainException($err, 404);                                
        }  
		
		return $action;
	}

	/**
	 * @return	bool
	 */
	public function isPublicAccess()
	{
		return $this->isPublic;
	}

	/**
	 * @return	bool
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
	 * @return	bool
	 */
	public function isAclForEachMethod()
	{
		return $this->isAclForEachMethod;
	}

	/**
	 * @param	string	$code
	 * @return	bool
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

		if ($this->isAclForEachMethod()) {
			if (! is_string($method) || 
				! isset($this->aclMap[$method]) ||
				! is_array($this->aclMap[$method])) {
				return false;
			}
			$map = $this->aclMap[$method];
		}
		else {
			$map = $this->aclMap;
		}

		$result = array_intersect($map, $compare);

		if (empty($result)) {
			return false;
		}

		return true;
	}

	/**
	 * @param	string	$method
	 * @return	string | false
	 */
	protected function getNameInMap($method)
	{
		if (! is_string($method) || ! isset($this->actionMap[$method])) {
			return false;
		}

		return $this->actionMap[$method];
	}

	/**
	 * @return	bool
	 */
	protected function isMapEmpty()
	{
		return empty($this->actionMap);
	}

	/**
	 * @return	array
	 */
	protected function getActionMap()
	{
		return $this->actionMap;
	}

	/**
	 * @param	array	$map
	 * @return	RouteAction
	 */
	protected function setActionMap(array $map)
	{
		foreach ($map as $method => $action) {
			if (! is_string($method) || empty($method)) {
				$err = "action map method must be a non empty string";
				throw new DomainException($err);
			}

			if (! is_string($action) || empty($action)) {
				$err = "action map action must be a non empty string";
				throw new DomainException($err);
			}
		}

		$this->actionMap = $map;
		return $this;
	}

	/**
	 * @return	string
	 */
	protected function getName()
	{	
		return $this->name;
	}

	/**
	 * @param	string	$name
	 * @return	RouteAction
	 */
	protected function setName($name)
	{
		if (! is_string($name) || empty($name)) {
			$err = "action name must be a non empty string";
			throw new DomainException($err);
		}

		$this->name = $name;
		return $this;
	}

	/**
	 * @param	string	$ns
	 * @return	null
	 */
	protected function setNamespace($ns)
	{
		if (! is_string($ns)) {
			$err = "mvc action namespace must be a string";
			throw new DomainException($err);
		}

		$this->namespace = $ns;
	}

	/**
	 * @param	array
	 * @return	null
	 */
	protected function setAclMap(array $map)
	{
		if ($map !== array_values($map)) {
			$this->validateMappedAclCodes($map);
			$this->isAclForEachMethod = true;
		}
		else {
			$this->validateAclCodes($map);
			$this->isAclForEachMethod = false;
		}

		$this->aclMap = $map;
	}

	/**
	 * @return	array
	 */
	protected function getAclMap()
	{
		return $this->aclMap;
	}

	/**
	 * @param	array	$codes
	 * @return	bool
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
	 * @param	array	$codes
	 * @return	bool
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
