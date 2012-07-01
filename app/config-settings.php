<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
$base = AF_BASE_PATH;
if (defined('AF_ALTERNATE_BASE_PATH')) {
    $base = AF_ALTERNATE_BASE_PATH;
}
return array(
    'prod' => array(
		'base-path'					=> array($base),
		'php-include-path'          => array($base),
		'php-include-path-action'	=> 'replace',
		'fault-handler-class'	    => 'Appfuel\Kernel\FaultHandler',
		'php-autoloader'		    => 'Appfuel\ClassLoader\StandardAutoLoader',
		'php-default-timezone'	    => 'America/Los_Angeles',
		'php-display-errors'	    => 'off',
		'php-error-level'		    => 'all, strict',
		'db'					    => array(),
		'startup-tasks'		        => array(),
		'pre-filters'		        => array(),
		'post-filters'		        => array(),
    ),
    'dev' => array(
		'php-display-errors'	    => 'on',
    )
);
