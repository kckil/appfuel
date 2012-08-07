<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Route;


interface UriMatcherInterface
{
    /**
     * @return  string
     */
    public function getUriPath();

    /**
     * @return  string
     */
    public function getUriScheme();

    /**
     * @return  string
     */
    public function getHttpMethod();

    /**
     * @return  array
     */
    public function getCaptures();

    /**
     * @param   array   $params
     * @return  null
     */
    public function setCaptures(array $list);
}
