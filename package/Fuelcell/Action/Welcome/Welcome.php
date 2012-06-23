<?php
/**                                                                             
 * Appfuel                                                                      
 * PHP 5.3+ object oriented MVC framework supporting domain driven design.      
 *                                                                              
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>                 
 * See LICENSE file at the project root directory for details.                  
 */
namespace Fuelcell\Action\Welcome;

use Appfuel\Kernel\Mvc\MvcAction;

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
        $view = $context->getView();
        $data = ['foo' => 'bar'];


        if ($this->isViewTemplate($view)) {
            $view->load($data);
        }
    }
}
