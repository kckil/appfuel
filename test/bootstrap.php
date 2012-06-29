<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\App\AppHandlerInterface;

$ctrl = array(
    'app-type'        => 'cli',
    'config-settings' => array(
        'php-include-path-action' => 'append'
    ),
);
$handler = require realpath(__DIR__ . '/../app/app-header.php');
