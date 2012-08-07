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
     * @return  string
     */
    public function getKey();

    /**
     * @return  string
     */
    public function getPattern();

    /**
     * @return  string
     */
    public function getController();

    /**
     * @return  string
     */
    public function getControllerMethod();

    /**
     * @return  array
     */
    public function getParams();

    /**
     * @return  string | null
     */    
    public function getHttpMethod();

    /**
     * Return true when http method is not null false otherwise
     *
     * @return  bool
     */
    public function isHttpMethodCheck();
}
