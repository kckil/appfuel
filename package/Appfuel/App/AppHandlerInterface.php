<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\App;

use Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\MvcContextInterface;

interface AppHandlerInterface
{
    public function getAppFactory();
    public function composeView(MvcContextInterface $context);
    public function runAction(MvcContextInterface $context);
    public function runTasks(array $tasks);
}
