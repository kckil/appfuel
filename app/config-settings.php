<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
return [
    'production' => [
		'base-path'					=> AF_BASE_PATH,
		'php-include-path'          => [AF_CODE_PATH],
		'php-include-path-action'	=> 'replace',
		'fault-handler-class'	    => 'Appfuel\Kernel\FaultHandler',
		'php-autoloader'		    => 'Appfuel\ClassLoader\StandardAutoLoader',
		'php-default-timezone'	    => 'America/Los_Angeles',
		'php-display-errors'	    => 'off',
		'php-error-level'		    => 'all, strict',
		'db'					    => [],
		'startup-tasks'		        => ['Appfuel\View\ViewStartupTask'],
		'pre-filters'		        => [],
		'post-filters'		        => [],
    ],
    'dev' => [
		'php-display-errors'	    => 'on',
    ]
);
