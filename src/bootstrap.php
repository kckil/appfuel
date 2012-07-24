<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */

function loadAutoloadScript($file)
{

    $file = realpath($file);
    if (file_exists($file)) {
        return require_once $file;
    }
}

if ((!$loader = loadAutoloadScript(__DIR__ . '/../../../autoload.php')) &&
    (!$loader = loadAutoloadScript(__DIR__ . '/../vendor/autoload.php'))) {
    die('Could not find autoload.php in vendor directory, make sure' .PHP_EOL.
        'you used composer to install your dependencies'. PHP_EOL);
}

return $loader;
