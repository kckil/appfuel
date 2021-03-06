<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

interface RouteActionSpecInterface
{
    /**
     * @param   array   $spec
     * @return  RouteAction
     */
    public function __construct(array $spec);

    /**
     * @param   string  $method 
     * @param   bool    $isQualified 
     * @return  string | false
     */
    public function findAction($method = null, $isQualified = true);

    /**
     * @return  string
     */
    public function getNamespace();

    /**
     * @throws  DomainException
     * @param   string  $method
     * @return  MvcActionInterface
     */
    public function createAction($method = null);

    /**
     * @return bool
     */
    public function isInput();

    /**
     * @return  array
     */
    public function getInput();
}
