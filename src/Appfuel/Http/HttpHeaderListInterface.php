<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

/**
 * Manages a list of http headers
 */
interface HttpHeaderListInterface
{
    /**
     * Returns the current header in the list
     *
     * @return  null
     */
    public function getHeader();

    /**
     * @throws  InvalidArgumentException
     * @param   string  $header
     * @return  null
     */
    public function addHeader($header);

    /**
     * @return  array
     */
    public function getAllHeaders();

    /**
     * case insensitive search through the headers
     * 
     * @param   string  $header
     * @return  bool
     */
    public function isHeader($header);

    /**
     * Load a list of headers into the header list
     * 
     * @param   array   $headers
     * @return  null
     */
    public function loadHeaders(array $headers);
}
