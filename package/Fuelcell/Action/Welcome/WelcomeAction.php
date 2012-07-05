<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Fuelcell\Action\Welcome;

use Appfuel\Kernel\Mvc\MvcController,
    Appfuel\Kernel\Mvc\MvcContextInterface;

/**
 * Fuelcell's welcome page.
 */
class WelcomeAction extends MvcController
{
    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function execute(MvcContextInterface $context)
    {
        $view = $context->getView();
        $view->setContent("welcome to appfuel");
    }
}
