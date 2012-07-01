<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use DomainException,
    Appfuel\View\ViewInterface,
    Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface,
    Appfuel\Console\ConsoleOutputInterface;

class ConsoleHandler extends AppHandler implements ConsoleHandlerInterface
{
    /**
     * @var  ConsoleOutputInterface
     */
    protected $output = null;

    /**
     * @param   ConsoleOutputInterface  $output 
     * @return  ConsoleHandler
     */
    public function __construct(ConsoleOutputInterface $output = null)
    {
        if (null === $output) {
            $output = $this->getAppFactory()
                           ->createConsoleOutput();
        }

        $this->setConsoleOutput($output);
    }

    /**
     * @return  ConsoleOutputInterface
     */
    public function getConsoleOutput()
    {
        return $this->output;
    }

    /**
     * @param   ConsoleOutputInterface $output
     * @return  ConsoleHandler
     */
    public function setConsoleOutput(ConsoleOutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @param   array    $data
     * @return  AppInputInterface
     */
    public function createConsoleInput(array $data)
    {
        $factory = $this->getAppFactory();
        $parser  = $factory->createCliArgParser();
        return $factory->createInput('cli', $parser->parse($data));
    }

    /**
     * @param   string  $text
     * @return  null
     */
    public function outputErrorToConsole($text)
    {
        $this->getConsoleOutput()
             ->renderError($text);
    }

    public function outputToConsole($data)
    {

    }

    /**
     * @param    MvcContextInterface $context
     * @return    null
     */
    public function outputContextToConsole(MvcContextInterface $context)
    {
        $content = $this->composeView($route, $context);
        $output  = $this->getAppFactory()
                        ->createConsoleOutput();
        
        $output->render($content);
    }

}
