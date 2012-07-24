<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\Kernel\AppInitializer,
    Appfuel\Kernel\ConsoleHandler;

$loader = require_once __DIR__ . '/../src/bootstrap.php';
if (isset($settings['autoload-classmap'])) {
    $classMap = $settings['autoload-classmap'];
    if (! is_array($classMap)) {
        throw new OutOfRangeException("autoload class map must be an array");
    }

    foreach ($classMap as $prefix => $paths) {
        $loader->add($prefix, $paths);
    }
}

$init = new AppInitializer();

return $init->showErrors()
            ->enableFullErrorReporting()
            ->registerAppfuelFaultHandler()
            ->createConsoleHandler();
