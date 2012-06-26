<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Console;

/**
 * Parse the long, short options as well as command line arguments
 */
interface ArgParserInterface
{
    /**
     * @param   array   $list
     * @return  array
     */
    public function parse(array $args);
}
