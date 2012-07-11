<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Route;

use OutOfBoundsException,
    InvalidArgumentException;

interface RouteUriSpecInterface
{
    /**
     * @param   array   $data
     * @return  RoutePattern
     */
    public function __construct(array $data);

    /**
     * @return  string
     */
    public function generate(array $parts = null);

    /**
     * @return  array
     */
    public function getStaticParts();

    /**
     * @return  array
     */
    public function getPathParams();

    /**
     * @return array
     */
    public function getDefault($param = null);
}
