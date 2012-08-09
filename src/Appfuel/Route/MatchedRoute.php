<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

use Exception,
    InvalidArgumentException;

class MatchedRoute implements MatchedRouteInterface
{
    /**
     * @var RouteSpecInterface
     */
    protected $spec = null;

    /**  
     * @var array
     */ 
    protected $captures = array();

    /**
     * @param   array $spec
     * @return  RouteCollection
     */
    public function __construct(RouteSpecInterface $spec, array $captures=null)
    {
        $this->spec = $spec;
        if (null !== $captures) {
            $this->setCaptures($captures);
        }
    }
    
    /**
     * @return  string
     */
    public function getKey()
    {
        return $this->getSpec()
                    ->getKey();
    }

    /**
     * @return  string
     */
    public function getSpec()
    {
        return $this->spec;
    }

    /**
     * @return  array | object | Closure
     */
    public function createCallableController()
    {
        $spec = $this->getSpec();
        $ctrl = $spec->getController();
        if (is_callable($ctrl)) {
            return $ctrl;
        }
       
        $action = new $ctrl();
        $method = $spec->getControllerMethod();
        if (null === $method) {
            $method = 'execute';
        }
        $call = array($action, $method);
        if (! is_callable($call)) {
            $err = "could not create callable action -($ctrl, $method)";
            throw new LogicException($err);
        }
            
        return $call;
    }

    /**
     * @return  array
     */
    public function getCaptures()
    {
        return $this->captures;
    }

    /**
     * @return  array
     */
    public function getCapturedValues()
    {
        return array_values($this->captures);
    }

    /**
     * @param   array   $params
     * @return  null
     */
    protected function setCaptures(array $list)
    {
        $this->captures = $list;
    }
}
