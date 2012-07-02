<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */ 
namespace Appfuel\Kernel\Mvc;

/**
 * This interface will be used by the Dispatcher for executing a particular 
 * startegy. Most often used my ActionController. 
 */
interface ExecutableInterface
{
    /**
     * @param   MvcContextInterface $context
     * @return  null
     */
    public function execute(MvcContextInterface $context);
}
