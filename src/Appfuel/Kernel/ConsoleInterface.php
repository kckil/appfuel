<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use Appfuel\Console\ConsoleInputInterface;

interface ConsoleInterface extends ApplicationInterface
{
    /**
     * @return  ConsoleInterface
     */
    public function getInput();

    /**
     * @param   ConsoleInputInterface  $parser
     * @return  ConsoleInterface
     */
    public function setInput(ConsoleInputInterface $input);
    
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
}
