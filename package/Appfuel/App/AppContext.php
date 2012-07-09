<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use DomainException,
    InvalidArgumentException,
    Appfuel\Http\HttpInputInterface,
    Appfuel\Console\ConsoleInputInterface,
    Appfuel\DataStructure\ArrayData,
    Appfuel\DataStructure\ArrayDataInterface,
    Appfuel\Kernel\Mvc\MvcAclInterface,
    Appfuel\Kernel\Mvc\MvcViewInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 */
class AppContext extends ArrayData implements MvcContextInterface
{
    /**
     * The context can be http or cli but nothing else
     * @var string
     */
    protected $type = 'http';

    /**
     * Actual route key used in user request
     * @var string
     */
    protected $routeKey = null;

    /**
     * Holds most of the user input given to the application. Used by the
     * Front controller and all action controllers
     * @var HttpInputInterface | ConsoleInputInterface
     */
    protected $input = null;

    /**
     * List of acl roles for this context. The dispatcher asks the mvc action
     * if this context will be allowed for processing based on these codes.
     * @var MvcAclInterface
     */
    protected $acl = null;

    /**
     * Used to hold a string or object that implements __toString
     * @var MvcAppViewInterface
     */
    protected $view = null;

    /**
     * The exit code is used by the framework to provide an exit status code
     * @var int
     */
    protected $exitCode = 200;

    /**
     * @param   string  $key    route key used to find the mvc action
     * @param   string  $type   cli | http only
     * @param   mixed   $input  object used to hold input data
     * @param   mixed   $array  acl codes used in acl access checks
     * @return    AppContext
     */
    public function __construct($key, $type, $input = null, $acl = null)
    {
        if ('http' !== $type && 'cli' !== $type) {
            $err = "context type must be http or cli";
            throw new DomainException($err);
        }
        $this->type = $type;
        $this->setRouteKey($key);

        if (null !== $input && 'http' === $type) {
            $this->setHttpInput($input);
        }
        else if (null !== $input && 'cli' === $type) {
            $this->setConsoleInput($input);
        }

        if (! $acl instanceof MvcAclInterface) {
            $this->acl = new AppAcl();
        }

        if (null !== $acl) {
            $this->setAcl($acl);
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return  bool
     */
    public function isCli()
    {
        return 'cli' === $this->type;
    }

    /**
     * @return  bool
     */
    public function isHttp()
    {
        return 'http' === $this->type;
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
        return $this->getView()
                    ->getFormat();
    }

    /**
     * @param    string    $format
     * @return    MvcContext
     */
    public function setViewFormat($format)
    {
        $this->getView()
             ->setFormat($format);

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
     * @param   mixed   $view
     * @return  bool
     */
    public function isValidView($view)
    {
        if (! $view instanceof MvcViewInterface) {
            return false;
        }

        return true;
    }

    /**
     * @param   MvcViewInterface $view
     * @return  AppContext
     */
    public function setView($view)
    {
        if (! $this->isValidView($view)) {
            $err = "view must implement Appfuel\Kernel\Mvc\MvcViewInterface";
            throw new DomainException($err);
        }
        $this->view = $view;
        return $this;
    }

    /**
     * @return    array
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param   array | MvcAclInterface $codes
     * @return  AppContext
     */
    public function setAcl($codes)
    {
        if (is_array($codes)) {
            $this->acl->load($codes);
        }
        else if ($codes instanceof MvcAclInterface) {
            $this->acl = $codes;
        }
        else {
            $err  = "acl codes must be an array of codes or an object that ";
            $err .= "implements Appfuel\Kernel\Mvc\MvcAclInterface";
            throw new DomainException($err);
        }

        return $this;
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
     * @param   mixed   $input
     * @return  bool
     */
    public function isValidHttpInput($input)
    {
        return $input instanceof HttpInputInterface;
    }

    /**
     * @param   mixed   $input
     * @return  bool
     */
    public function isValidConsoleInput($input)
    {
        return $input instanceof ConsoleInputInterface;
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
        $context->setView($this->getView());
        $context->load($this->getAll());
        $context->setExitCode($this->getExitCode());

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

    /**
     * @param   unkown   $input
     * @return  null
     */
    protected function setHttpInput($input)
    {
        if (! $this->isValidHttpInput($input)) {
            $err = "input is not valid for an http context";
            throw new DomainException($err);
        }

        $this->input = $input;
    }

    /**
     * @param   unkown   $input
     * @return  null
     */
    protected function setConsoleInput($input)
    {
        if (! $this->isValidConsoleInput($input)) {
            $err = "input is not valid for an console context";
            throw new DomainException($err);
        }

        $this->input = $input;
    }
}
