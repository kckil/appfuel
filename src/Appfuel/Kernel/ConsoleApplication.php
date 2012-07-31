<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel;

use DomainException,
    Appfuel\Console\ConsoleInputInterface;

class ConsoleApplication extends Application implements ConsoleInterface
{
    /**
     * @var ConsoleInputInterface
     */
    protected $input = null;

    /**
     * @return  ConsoleInputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param   ConsoleInputInterface   $input
     * @return  ConsoleApplication
     */
    public function setInput(ConsoleInputInterface $input)
    {
        $this->input = $input;
        return $this;
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
