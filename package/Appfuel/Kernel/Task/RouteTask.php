<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Kernel\Task;

use DomainException,
    Appfuel\App\AppRegistry,
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
        $path = AppRegistry::getAppPath();
        echo "<pre>", print_r($path, 1), "</pre>";exit;
        $finder = new FileFinder();
        $reader = new FileReader($finder);
        $routes = $reader->decodeJsonAt('routes.json');
        if (! $routes) {
    
        }
    }
}
