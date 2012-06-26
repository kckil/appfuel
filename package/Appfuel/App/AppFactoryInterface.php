<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\App;

use RunTimeException,
    Appfuel\Kernel\Mvc\AppInputInterface,
    Appfuel\Kernel\Mvc\RequestUriInterface,
    Appfuel\Kernel\Mvc\MvcDispatcherInterface,
    Appfuel\Kernel\Mvc\InterceptChainInterface;

/**
 * Encapsulates the create of the frameworks most critical objects so that
 * may be easily replaced.
 */
interface AppFactoryInterface
{    
}
