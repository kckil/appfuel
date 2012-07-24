<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Console;

/**
 * Provides validation to ensure scalar data or objects that implement
 * __toString. Will render to the standard output stream and will render
 * errors to the standard error stream
 */
interface ConsoleOutputInterface
{
    /**
     * Write to the STDOUT.
     *
     * @param   scalar|object   $data
     * @return  null
     */
    public function render($data);

    /**
     * Write to the STDERR.
     *
     * @param   scalar|object  $data
     * @return  null
     */
    public function renderError($data);
}
