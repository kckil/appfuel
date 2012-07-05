<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

interface MatchedRouteInterface
{
    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data);

    /**
     * @return  string
     */
    public function getRouteKey();

    /**
     * @return  string
     */
    public function getFormat();

    /**
     * @return  bool
     */
    public function isFormat();

    /**
     * @return  string
     */
    public function getGroup();

    /**
     * @return  string
     */
    public function getGroupMatch();

    /**
     * @return  string
     */
    public function getRouteMatch();

    /**
     * @return  array
     */
    public function getCaptures();
}
