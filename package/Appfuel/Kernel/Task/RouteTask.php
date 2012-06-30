<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
    Appfuel\Filesystem\FileFinder,
    Appfuel\Filesystem\FileReader,
    Appfuel\Kernel\Route\RouteRegistry;

class RouteTask extends StartupTask
{
    /**
     * @return  null
     */
    public function execute()
    {
        $finder = new FileFinder('app/build');
        $reader = new FileReader($finder);
        $routes = $reader->decodeJsonAt('routes.json');
        if (! $routes) {
    
        }
    }
}
