<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details. 
 */
namespace Appfuel\App;

use LogicException,
    DomainException,
    RunTimeException,
    InvalidArgumentException,
    Appfuel\View\ViewInterface,
    Appfuel\ClassLoader\ManualClassLoader,
    Appfuel\Config\ConfigRegistry,
    Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * 
 */
class CliHandler extends AppHandler implements CliHandlerInterface
{

    /**
     * @param   array   $tasks  default null
     * @return  CliHandler
     */
    public function initialize(array $tasks = null)
    {
        parent::initialize($tasks);
        $factory = $this->getAppFactory();
    }

    /**
     * @param   array    $data
     * @return  AppInputInterface
     */
    public function createConsoleInput(array $data)
    {
        return $this->getAppFactory()
                    ->createConsoleInput($data);
    }

    public function outputErrorToConsole($text)
    {

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
