<?php
/**                                                                              
 * Appfuel                                                                       
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.       
 *                                                                               
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at the project root directory for details. 
 */
namespace Appfuel\App;

use Appfuel\Kernel\TaskHandlerInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

interface ConsoleHandlerInterface extends AppHandlerInterface
{
    /**
     * @param   array   $data
     * @return  AppInputInterface
     */
    public function createConsoleInput(array $data);

    /**
     * @param   string  $text
     * @return  null
     */
    public function outputErrorToConsole($text);

    /**
     * @param   string  $data
     * @return  null
     */
    public function outputToConsole($data);

    /**
     * @param    MvcContextInterface    $context
     * @return  null
     */
    public function outputContextToConsole(MvcContextInterface $context);
}
