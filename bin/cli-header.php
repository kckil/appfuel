<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\Console\ArgParser,
    Appfuel\Console\ConsoleInput,
    Appfuel\Kernel\ConsoleApplication;

if (PHP_SAPI !== 'cli') {
    throw new Exception("this script is intented to be run in the console");
}

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

$argParser = new ArgParser();
$input = new ConsoleInput($argParser->parse($_SERVER['argv']));
if (! $input->isOption('root-path', 'r')) {
    fwrite(STDERR, "root path of the app is missing -(root-path|r) \n");
    exit(1);
}
$root = $input->getOption('root-path', 'r');

if ($input->isOption('env', 'e')) {
    $env = $input->getOption('env', 'e');
}
else if (! ($env = getenv('AF_ENV'))) {
    $env = 'production';
}


$console = new ConsoleApplication($root, $env, true);
$console->setInput($input);
return $console;
