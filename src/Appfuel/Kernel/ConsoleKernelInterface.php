<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use Appfuel\Console\ArgParserInterface;

interface ConsoleKernelInterface extends AppKernelInterface
{
    /**
     * @param   array   $data
     * @param   ArgParserInterface  $parser
     * @return  ConsoleInput
     */
    public function createConsoleInput(array $argv = null, 
                                       ArgParserInterface $parser = null);
    /**
     * @param   string  $text
     * @return  null
     */
    public function outputError($text);

    /**
     * @param   string  $data
     * @return  null
     */
    public function output($data);

    /**
     * @return  ArgParser
     */
    public function createArgParser();
}
