<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use DomainException,
    InvalidArgumentException;
/**
 */
class RouteGroup implements RouteGroupInterface
{
    /**
     * @var bool
     */
    protected $group = null;

    /**
     * @var string
     */
    protected $originalUri = null;

    /**
     * @var string
     */
    protected $matched = null;

    /**
     * @var array
     */
    protected $captures = array();

    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data)
    {
    }

    /**
     * @return  string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return  string
     */
    public function getMatched()
    {
        return $this->matched;
    }

    /**
     * @return  string
     */
    public function getUri()
    {
        return $this->uri;
    }

    public function getCaptures()
    {

    }
}
