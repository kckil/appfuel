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
class HttpOutput
{
    /**
     * @param   HttpResponseInterface $response
     * @return  null
     */
    public static function render(HttpResponseInterface $response)
    {
        if (headers_sent()) {
            return;
        }

        header($response->getStatusLine());
        
        $headerList = $response->getHeaderList();

        $replaceSimilar = false;
        foreach($headerList as $header) {
            header($header, $replaceSimilar);
        }

        echo $response->getContent();
    }
}
