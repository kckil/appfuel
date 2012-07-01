<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use InvalidArgumentException,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 */
class AppContext extends ArrayData implements MvcContextInterface
{
    /**
     * Actual route key used in user request
     * @var string
     */
    protected $routeKey = null;

    /**
     * Holds most of the user input given to the application. Used by the
     * Front controller and all action controllers
     * @var    AppInputInterface
     */
    protected $input = null;

    /**
     * List of acl roles for this context. The dispatcher asks the mvc action
     * if this context will be allowed for processing based on these codes.
     * @var    array
     */
    protected $aclCodes = array();

    /**
     * Used to hold a string or object that implements __toString
     * @var mixed
     */
    protected $view = '';

    /**
     * Data structure used to hold all the view assignments
     * @var ArrayDataInterface
     */
    protected $viewData = null;

    /**
     * View format as determined by encoded route information. ex) route.json
     * @var string
     */
    protected $format = null;

    /**
     * The exit code is used by the framework to provide an exit status code
     * @var int
     */
    protected $exitCode = 200;

    /**
     * @param    string    $strategy    console|ajax|html
     * @param    AppInputInterface    $input
     * @return    AppContext
     */
    public function __construct($key,
                                AppInputInterface $input, 
                                $viewData = null)
    {
        $this->setRouteKey($key);
        $this->setInput($input);
        if (null === $viewData) {
            $viewData = new ArrayData();
        }
        else {
            if (is_array($viewData)) {
                $viewData = new ArrayData($viewData);
            }
            else if (! $viewData instanceof ArrayDataInterface) {
                $err  = "view data must be an array or an object that -(";
                $err .= "implements Appfuel\DataStructure\ArrayDataInterface)";
                throw new DomainException($err);
            }
        }
        $this->setViewData($viewData);
    }

    /**
     * @return    string
     */
    public function getRouteKey()
    {
        return $this->routeKey;
    }

    /**
     * @return    string
     */
    public function getViewFormat()
    {
        return $this->format;
    }

    /**
     * @param    string    $format
     * @return    MvcContext
     */
    public function setViewFormat($format)
    {
        if (! is_string($format)) {
            $err = 'view format must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->format = $format;
        return $this;
    }

    /**
     * @return    
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param    mixed    $view
     * @return    bool
     */
    public function isValidView($view)
    {
        if (is_scalar($view) ||
            (is_object($view) && is_callable(array($view, '__toString')))) {
            return true;
        }
    
        return false;
    }

    /**
     * @return    bool
     */
    public function isContextView()
    {
        return $this->isValidView($this->view);
    }

    /**
     * @param    ViewTemplateInterface $template
     * @return    AppContext
     */
    public function setView($view)
    {
        if (! $this->isValidView($view)) {
            $err  = 'view must be a scalar value or an object that ';
            $err .= 'implements __toString';
            throw new InvalidArgumentException($err);
        }

        $this->view = $view;
        return $this;
    }

    /**
     * @return  ArrayDataInterface
     */
    public function getViewData()
    {
        return $this->viewData;
    }

    /**
     * @param   ArrayDataInterface $data
     * @return  AppContext
     */
    public function setViewData(ArrayDataInterface $data)
    {
        $this->viewData = $data;
        return $this;
    }

    /**
     * @return    array
     */
    public function getAclCodes()
    {
        return $this->aclCodes;
    }

    /**
     * @param    string    $code
     * @return    AppContext
     */
    public function addAclCode($code)
    {
        if (empty($code) || ! is_string($code)) {
            throw new InvalidArgumentException(
                'role code must be a non empty string'
            );
        }
    
        if ($this->isAclCode($code)) {
            return $this;    
        }

        $this->aclCodes[] = $code;
        return $this;
    }

    /**
     * @param    string    $code
     * @return    bool
     */
    public function isAclCode($code)
    {
        if (empty($code) || 
            ! is_string($code) || ! in_array($code, $this->aclCodes, true)) {
            return false;
        }

        return true;
    }

    /**
     * @return    int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }
    
    /**
     * @param    int    $code
     * @return    AppContext
     */
    public function setExitCode($code)
    {
        if (! is_int($code)) {
            throw new InvalidArgumentException('exit code must be an integer');
        }
        $this->exitCode = $code;
        return $this;
    }

    /**
     * @return  ContextInputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param    string    $key
     * @param    AppInputInterface $input
     * @return   AppContext
     */
    public function cloneContext($key, MvcContextInterface $input = null)
    {
        if (null === $input) {
            $input = $this->getInput();
        }

        $context = new self($key, $input);
        if ($this->isContextView()) {                              
            $context->setView($this->getView());
        }

        $context->load($this->getAll());
        $format = $this->getViewFormat();
        if (is_string($format)) {
            $context->setViewFormat($format);
        }

        return $context;
    }

    /**
     * @param   MvcContextInterface
     * @return  MvcContextInterface
     */
    public function merge(MvcContextInterface $context, $isReplaceInput = false)
    {
        $view = $context->getView();                                                 
        if ($this->isValidView($view)) {                                      
            $this->setView($view);                                            
            $format = $context->getViewFormat();                                     
            if (is_string($format)) {                                            
                $this->setViewFormat($format);                                
            }                                                            
        }

        $this->load($contex->getAll());
        $this->setExitCode($context->getExitCode());

        $codes = $context->getAclCodes();
        foreach ($codes as $code) {
            $this->addAclCode($code);
        }

        if (true === $isReplaceInput) {
            $this->setInput($context->getInput());
        }
        
        return $context;
    }

    /**
     * @param    AppInputInterface    $input
     * @return    null
     */
    protected function setInput(AppInputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * @param    string    $key
     * @return    null
     */
    protected function setRouteKey($key)
    {
        if (! is_string($key)) {
            $err = 'route key must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->routeKey = $key;
    }
}
