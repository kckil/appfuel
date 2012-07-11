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
class ConsoleOutput implements ConsoleOutputInterface
{
    /**
     * Its the Output engines responsiblity to validate the output is 
     * is safe to use.
     * 
     * @param    mixed    $data
     * @return    null
     */
    public function render($data, $includeNewline = true)
    {
        if (! $this->isValidOutput($data)) {
            $err = 'invalid console output: must be able to cast to a string';
            $this->renderError($err);
            exit;
        }

        if (true === $includeNewline) {
            $data .= PHP_EOL;
        }

        if (PHP_SAPI !== 'cli') {
            echo $data;
            return;
        }

        fwrite(STDOUT, $data);
    }

    /**
     * @param   string    $msg    error message
     * @param   int        $code    ignored by commandline
     * @return  null
     */
    public function renderError($data, $includeNewline = true)
    {
        if (! $this->isValidOutput($data)) {
            $err = 'unkown error has occured: also error msg is not valid';
            fwrite(STDERR, $err);
            exit;
        }
    
        if (true === $includeNewline) {
            $data .= PHP_EOL;
        }

        if (PHP_SAPI !== 'cli') {
            echo $data;
            return;
        }

        fwrite(STDERR, (string)$data);
    }

    /**
     * @param   mixed   $data
     * @return  bool
     */
    protected function isValidOutput($data)
    {
        if (is_scalar($data) || 
            is_object($data) && is_callable(array($data, '__toString'))) {
            return true;
        }

        return false;
    }
}
