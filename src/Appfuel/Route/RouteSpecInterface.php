<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;

interface RouteSpecInterface
{
    /**
     * @param   array   $data
     * @return  RouteSpecInterface
     */
    public function __construct(array $data);

    /**
     * @return  string
     */
    public function getKey();

    /**
     * @return  string
     */
    public function getPattern();

    /**
     * @param   string  $class
     * @return  null
     */
    public function getController();
}
