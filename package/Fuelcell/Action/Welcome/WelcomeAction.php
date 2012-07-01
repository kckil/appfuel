<?php
/**                                                                             
 * Appfuel                                                                      
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.      
 *                                                                              
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                 
 * See LICENSE file at the project root directory for details.                  
 */
namespace Fuelcell\Action\Welcome;

use Appfuel\Kernel\Mvc\MvcAction,
    Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * Fuelcell's welcome page.
 */
class WelcomeAction extends MvcAction
{
    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function process(MvcContextInterface $context)
    {
        $data = $context->getViewData();
        $data->add('foo', 'bar');
        
        $format = $context->getViewFormat();
        $view = $this->composeView($format, $data);
        
        $context->setView($view);
    }
}
