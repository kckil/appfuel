<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException;

/**
 * Controls which pre and post intercept filters are applied
 */
class RouteInterceptFilterSpec implements RouteInterceptFilterSpecInterface
{
    /**
     * Framework will skip all pre filters if this is true
     * @var bool
     */
    protected $isPreFilterEnabled = true;

    /**
     * List of intercept filters used before the action is processed
     * @var array
     */
    protected $preFilters = array();

    /**
     * List of pre intercept filters to be exclude 
     * @var array
     */
    protected $excludedPreFilters = array();

    /**
     * List of intercept filters used after the action is processed
     * @var array
     */
    protected $postFilters = array();

    /**
     * List of post intercept filters to be exclude 
     * @var array
     */
    protected $excludedPostFilters = array();

    /**
     * Framework will skip all post filters if this is true
     * @var bool
     */
    protected $isPostFilterEnabled = true;

    /**
     * @param   array   $spec
     * @return  RouteFilterSpec
     */
    public function __construct(array $spec)
    {
        if (isset($spec['disable-pre-filters']) && 
            true === $spec['disable-pre-filters']) {
            $this->isPreFilterEnabled = false;
        }
        else {

            if (isset($spec['pre'])) {
                $this->setPreFilters($spec['pre']);
            }

            if (isset($spec['exclude-pre'])) {
                $this->setExcludedPreFilters($spec['exclude-pre']);
            }
        }

        if (isset($spec['skip-post']) && true === $spec['skip-post']) {
            $this->isPostFilterEnabled = false;
        }
        else {

            if (isset($spec['post'])) {
                $this->setPostFilters($spec['post']);
            }

            if (isset($spec['exclude-post'])) {
                $this->setExcludedPostFilters($spec['exclude-post']);
            }
        }
    }

    /**
     * @return    bool
     */
    public function isPreFilteringEnabled()
    {
        return $this->isPreFilterEnabled;
    }

    /**
     * @return  array
     */
    public function getPreFilters()
    {
        return $this->preFilters;
    }

    /**
     * @return  bool
     */
    public function isPreFilters()
    {
        return ! empty($this->preFilters);
    }

    /**
     * @return  bool
     */
    public function isExcludedPreFilters()
    {
        return ! empty($this->excludedPreFilters);
    }

    /**
     * @return  array
     */
    public function getExcludedPreFilters()
    {
        return $this->excludedPreFilters;
    }

    /**
     * @return  bool
     */
    public function isPostFilteringEnabled()
    {
        return $this->isPostFilterEnabled;
    }

    /**
     * @return  array
     */
    public function getPostFilters()
    {
        return $this->postFilters;
    }

    /**
     * @return  bool
     */
    public function isPostFilters()
    {
        return ! empty($this->postFilters);
    }

    /**
     * @return  bool
     */
    public function isExcludedPostFilters()
    {
        return ! empty($this->excludedPostFilters);
    }

    /**
     * @return  array
     */
    public function getExcludedPostFilters()
    {
        return $this->excludedPostFilters;
    }

    /**
     * @param   string|array    $list
     * @return  null    
     */
    protected function setPreFilters($list)
    {
        $list = $this->filterList($list, 'pre');
        if (! $this->isValidFilterList($list)) {
            $err = "pre intercept filter must be a non empty string";
            throw new DomainException($err);
        }

        $this->preFilters = $list;
    }

    /**
     * @param   string|array $list
     * @return  null
     */
    protected function setExcludedPreFilters(array $list)
    {
        $list = $this->filterList($list, 'excluded pre');
        if (! $this->isValidFilterList($list)) {
            $err = "excluded pre intercept filter must be a non empty string";
            throw new DomainException($err);
        }

        $this->excludedPreFilters = $list;
        return $this;    
    }

    /**
     * @param   string|array    $list
     * @return  null    
     */
    protected function setPostFilters($list)
    {
        $list = $this->filterList($list, 'post');
        if (! $this->isValidFilterList($list)) {
            $err = "post intercept filter must be a non empty string";
            throw new DomainException($err);
        }

        $this->postFilters = $list;
    }

    /**
     * @param   array   $list
     * @return  RouteInterceptChain
     */
    public function setExcludedPostFilters($list)
    {
        $list = $this->filterList($list, 'excluded pre');
        if (! $this->isValidFilterList($list)) {
            $err = "excluded post intercept filter must be a non empty string";
            throw new DomainException($err);
        }

        $this->excludedPostFilters = $list;
    }

    /**
     * @param   string|array    $list
     * @return  array
     */
    protected function filterList($list, $filterType)
    {
        if (is_string($list)) {
            $list = array($list);
        }
        else if (! is_array($list)) {
            $err = "-($filterType) filters must be a string or an array";
            throw new DomainException($err);
        }

        return $list;
    }

    /**
     * @param   array   $list
     * @return  bool
     */
    protected function isValidFilterList(array $list)
    {
        foreach ($list as $filter) {
            if (! is_string($filter) || empty($filter)) {
                return false;
            }
        }

        return true;
    }
}
