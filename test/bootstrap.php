<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\App\AppHandlerInterface;

$header = realpath(__DIR__ . '/../app/app-header.php');
if (! file_exists($header)) {
    $err = "could not find the app header script";
    throw new RunTimeException($err);
}

$ctrl = array(
    'app-type' => 'cli'
);
require $header;
if (! isset($handler) || ! $handler instanceof AppHandlerInterface) {
    $err  = "app handler was not created or does not implement Appfuel\Kernel";    $err .= "\AppHandlerInterface";    throw new LogicException($err);
}

