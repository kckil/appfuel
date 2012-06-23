<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
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
     * @throws  DomainException
     * @param   array   $spec
     * @return  RouteAction
     */
    public function __construct(array $spec)
    {
        if (! isset($spec['namespace'])) {
            $err = "mvc action namespace -(namespace) is required but not set";
            throw new DomainException($err);
        }
        $this->setNamespace($spec['namespace']);

        if (! isset($spec['action'])) {
            $err  = 'the key -(action) must be set';
            throw new DomainException($err);
        }

        $this->setAction($spec['action']);
    }

    /**
     * @param   string $method 
     * @return  string | false
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
     * @return  string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @throws  DomainException
     * @param   string  $method
     * @return  MvcActionInterface
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
     * @param   string  $method
     * @return  string | false
     */
    protected function getNameInMap($method)
    {
        if (! is_string($method) || ! isset($this->actionMap[$method])) {
            return false;
        }

        return $this->actionMap[$method];
    }

    /**
     * @return  bool
     */
    protected function isMapEmpty()
    {
        return empty($this->actionMap);
    }

    /**
     * @return  array
     */
    protected function getActionMap()
    {
        return $this->actionMap;
    }

    protected function setAction($action)
    {
        if (is_string($action)) {
            $this->setName($action);
            return;
        }
        else if (! is_array($action)) {
            $err = "action must be a string or an array";
            throw new DomainException($err);
        }

        $this->setActionMap($action);
    }


    /**
     * @throws  DomainException
     * @param   array   $map
     * @return  RouteAction
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
     * @return  string
     */
    protected function getName()
    {    
        return $this->name;
    }

    /**
     * @throws  DomainException
     * @param   string  $name
     * @return  RouteAction
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
     * @throws  DomainException
     * @param   string    $ns
     * @return  null
     */
    protected function setNamespace($ns)
    {
        if (! is_string($ns)) {
            $err = "mvc action namespace must be a string";
            throw new DomainException($err);
        }

        $this->namespace = $ns;
    }
}
