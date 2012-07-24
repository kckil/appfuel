<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Console;

use DomainException,
    Appfuel\Kernel\AppKernel;

class ConsoleHandler extends AppKernel implements ConsoleHandlerInterface
{
    /**
     * @param   array   $data
     * @param   ArgParserInterface  $parser
     * @return  ConsoleInput
     */
    public function createConsoleInput(array $argv = null, 
                                       ArgParserInterface $parser = null)
    {
        if (null === $argv) {
            $argv = $_SERVER['argv'];            
        }

        if (null === $parser) {
            $parser  = $this->createArgParser();
        }

        return new ConsoleInput($parser->parse($argv));
    }

    /**
     * @param   string  $text
     * @return  null
     */
    public function outputError($text)
    {
        fwrite(STDERR, $text);
    }

    public function output($content)
    {
        fwrite(STDOUT, $content);
    }

    /**
     * @return  ArgParserInterface
     */
    public function createArgParser()
    {
        return new ArgParser();
    } 
}
