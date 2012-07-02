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
    Appfuel\Kernel\Mvc\MvcViewInterface;

/**
 * Holds the content string and data assignments used to contstruct the view.
 * A View Compositor will use a this to compose the view based on the data
 * and format then assign the result back to its content.
 */
class AppView extends ArrayData implements MvcViewInterface
{
    /**
     * Content to be displaced
     * @var string
     */
    protected $content = '';

    /**
     * View format as determined by encoded route information. ex) route.json
     * @var string
     */
    protected $format = null;

    /**
     * The view templating system uses packages to identify their locations. 
     * Used mostly for html and html-frag
     * @var string
     */
    protected $pkg = null;

    /**
     * @param   string 
     * @return  AppView
     */
    public function __construct(array $spec = null)
    {
        if (isset($spec['data'])) {
            $data = $spec['data']; 
            if ($data instanceof ArrayDataInterface) {
                $data = $data->getAll();
            }
            else if (! is_array($data)) {
                $err  = "view data must be an array or an object that -(";
                $err .= "implements Appfuel\DataStructure\ArrayDataInterface)";
                throw new DomainException($err);
            }
            parent::__construct($data);
        }

        if (isset($spec['content'])) {
            $this->setContent($spec['content']);
        }

        if (isset($spec['format'])) {
            $this->setFormat($spec['format']);
        }

        if (isset($spec['pkg'])) {
            $this->setPkgName($spec['pkg']);
        }
    }

    /**
     * @return    string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param   string $format
     * @return  AppView
     */
    public function setFormat($format)
    {
        if (! is_string($format)) {
            $err = 'view format must be a string';
            throw new InvalidArgumentException($err);
        }

        $this->format = $format;
        return $this;
    }

    /**
     * @param    mixed    $view
     * @return    bool
     */
    public function isValid($view)
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
    public function isEmpty()
    {
        return empty($this->content);
    }

    /**
     * @return    
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param   mixed   $view
     * @param   string  $action
     * @return  AppView
     */
    public function setContent($view, $action = 'replace')
    {
        if (! $this->isValid($view)) {
            $err  = 'view must be a scalar value or an object that ';
            $err .= 'implements __toString';
            throw new InvalidArgumentException($err);
        }

        switch ($action) {
            case 'append':  $content = $this->content . $view; break;
            case 'prepend': $content = $view . $this->content; break;
            default: 
                $content =(string) $view;
        }

        $this->content = $content;
        return $this;
    }

    /**
     * @return  string
     */
    public function getPkgName()
    {
        return $this->pkg;
    }

    /**
     * @param   string  $pkg
     * @return  AppView
     */
    public function setPkgName($pkg)
    {
        if (! is_string($pkg) || empty($pkg)) {
            $err = 'package name must be a non empty string';
            throw new DomainException($err);
        }

        $this->pkg = $pkg;
        return $this;
    }

    /**
     * @return  bool
     */
    public function isPkg()
    {
        return is_string($this->pkg) && ! empty($this->pkg);
    }

    /**
     * @return  string
     */
    public function __toString()
    {
        return $this->getContent();
    }
}
