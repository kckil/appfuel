<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Http;

/**
 * Handle specific details for outputting http data
 */
interface HttpOutputInterface
{
    /**
     * @param   HttpResponseInterface $response
     * @return  null
     */
    public function render(HttpResponseInterface $response);
}
