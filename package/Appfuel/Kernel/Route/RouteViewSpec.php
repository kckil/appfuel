<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException;

/**
 * Controls view settings like disabling the view or telling the framework
 * you will handle the view manually, what the default format for the view is
 * and in the case of html pages what what does this view use.
 */
class RouteViewSpec implements RouteViewSpecInterface
{
    /**
     * Used to determine what view format will be used with this route
     * @var string
     */
    protected $defaultFormat = 'html';

    /**
     * @var    bool
     */
    protected $isViewDisabled = false;

    /**
     * Determines if the framework needs to compose the view from the view data
     * @var bool
     */
    protected $isManualView = false;

    /**
     * Name of the view package which represents the view for this route.
     * View packages are generally html pages
     * @var string
     */
    protected $viewPkg = null;

    /**
     * @param   array   $spec
     * @return  RouteViewSpec
     */
    public function __construct(array $spec)
    {
        if (isset($spec['default-format'])) {
            $this->setDefaultFormat($spec['default-format']);
        }

        if (isset($spec['disable-view']) && true === $spec['disable-view']) {
            $this->isViewDisabled = true;
        }

        if (isset($spec['manual-view']) && true === $spec['manual-view']) {
            $this->isManualView = true;
        }

        if (isset($spec['view-pkg'])) {
            $this->setViewPackage($spec['view-pkg']);
        }
    }

    /**
     * @return  string
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     * @return  bool
     */
    public function isViewDisabled()
    {
        return $this->isViewDisabled;
    }

    /**
     * @return  bool
     */
    public function isManualView()
    {
        return $this->isManualView;
    }

    /**
     * @return  bool
     */
    public function isViewPackage()
    {
        return is_string($this->viewPkg) && ! empty($this->viewPkg);
    }

    /**
     * @return  string
     */
    public function getViewPackage()
    {
        return $this->viewPkg;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setViewPackage($name)
    {

        if (! is_string($name) || empty($name)) {
            $err = "view package name must be a non empty string";
            throw new DomainException($err);
        }

        $this->viewPkg = $name;
    }

    /**
     * @param   string  $name
     * @return  null
     */
    protected function setDefaultFormat($name)
    {
        if (! is_string($name)) {
            $err = 'route format must be a string';
            throw new DomainException($err);
        }

        $this->defaultFormat = $name;
    }
}
