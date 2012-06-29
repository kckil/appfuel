<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\App\AppDetail,
    Appfuel\App\AppFactory,
    Appfuel\App\AppRegistry,
    Appfuel\App\ConfigHandler,
    Appfuel\App\WebHandlerInterface,
    Appfuel\App\ConsoleHandlerInterface;

/*
 * variable which holds an array of data to control this script.
 */
if (! isset($ctlr)) {
    $ctlr = array();
}

/*
 * The base path is defined as the absolute path to the root of the application
 * this path is not configurable and is required for appfuel to function. 
 */
$ctrl['paths']['app-root'] = realpath(__DIR__ . '/../');
define('AF_BASE_PATH', $ctrl['paths']['app-root']);

/*
 * The app path is defined as the directory where all app configuration, 
 * mapping and build files are kept. Like base path it is not configurable
 * and is required for appfuel to function. All other directories can be
 * configured
 */
define('AF_APP_PATH', AF_BASE_PATH . DIRECTORY_SEPARATOR . 'app');
$ctrl['paths']['app-dir'] = AF_APP_PATH;

/*
 * The src path is defined as the directory that holds all the source code
 * for the application. Currently appfuel expects all source to live 
 * namespaced under a single directory, because it greatly simplifies the 
 * task of auto loading.
 */
if (! isset($ctrl['paths']['app-src'])) {
    $ctrl['paths']['app-src'] = AF_BASE_PATH . DIRECTORY_SEPARATOR . 'package';
}
define('AF_SRC_PATH', $ctrl['paths']['app-src']);

/*
 * Kernel dependencies are a list of files the need to be in memory before the
 * autoloader. This could be because of classes used before the autoloader 
 * task is execute or simply for effeciency.
 */ 
$dlist = require AF_APP_PATH . DIRECTORY_SEPARATOR . "kernel-dependencies.php";

/*
 * Allow the calling script to append dependency classes along with the 
 * framework dependencies. The array given must be an associative array of 
 * qualified class name => file path (relative to AF_CODE_PATH)
 */
if (! isset($ctrl['depend-action']) || ! is_string($ctrl['depend-action'])) {
    $ctrl['depend-action'] = 'append';
}

if (isset($ctrl['depend-list']) && is_array($ctrl['depend-list'])) {
    $dependList = $ctrl['depend-list'];
    if ($dependList === array_values($dependList)) {
        $err  = "dependency array given by the calling script must be an ";
        $err .= "associative array";
        throw new LogicException($err);
    }
    
    switch ($ctrl['depend-action']) {
        case 'prepend':
            $dlist = array_merge($dependList, $dlist);
            break;
        case 'replace':
            $dlist = $dependList;
            break;
        default:
            $dlist = array_merge($dlist, $dependList);
    }
}

/*
 * Actually load the files list as dependencies into memory.
 */
foreach ($dlist as $class => $file) {
    if (class_exists($class) || interface_exists($class, false)) {
        continue;    
    }

    require AF_SRC_PATH . DIRECTORY_SEPARATOR . $file;
}
unset($file, $dlist, $dependList, $class, $asbsolute, $err);

/*
 * AppDetail allows you to decouple directory names (except app-root a.k.a 
 * base path and app-dir)
 */
$detail  = new AppDetail($ctrl['paths']);
$factory = new AppFactory();
$taskHandler = $factory->createTaskHandler();

/*
 * Make this important object available to the application globally through
 * the AppRegistry
 */
AppRegistry::setAppDetail($detail);
AppRegistry::setAppFactory($factory);
AppRegistry::setTaskHandler($taskHandler);

$config = $factory->createConfigHandler();

/*
 * Override normal configuration by using $ctrl['config-settings'] for manual
 * config data and use $ctrl['config-action'] to replace or merge that data.
 */
if (! isset($ctrl['config-action']) || ! is_string($ctrl['config-action'])) {
    $ctrl['config-action'] = 'append';
}

/*
 * Allow the calling code to change the config file. Note: you must specify 
 * the absolute path to the config file you wish to load
 */
if (isset($ctrl['config-build-file'])) {
    $configFile = $ctrl['config-build-file'];
}
else {
    $configFile = $detail->getPath('app-config-build-file');
}
$settings = null;
if (isset($ctrl['config-settings']) && is_array($ctrl['config-settings'])) {
    $settings = $ctrl['config-settings'];
}

/*
 * If settings has config data and the action is replace then only use
 * settings, otherwise use config data as found in the config file and
 * if settings has data then merge with it.
 */
if (null !== $settings && 'replace' === $ctrl['config-action']) {
    $headerSettings = $settings;
}
else {
    $headerSettings = $config->getFileData($configFile);
    if (null !== $settings) {
        $headerSettings = array_replace_recursive($headerSettings, $settings);
    }
}

/*
 * Load the configuration settings to the application registry.
 */
AppRegistry::load($headerSettings);

/*
 * list of framework startup tasks to be run after initialization. The 
 * including script can append, prepend or replaces these when needed. 
 */
$tasks = array(
    'Appfuel\Kernel\Task\PHPIniTask',
    'Appfuel\Kernel\Task\PHPErrorTask',
    'Appfuel\Kernel\Task\PHPPathTask',
    'Appfuel\Kernel\Task\PHPDefaultTimezoneTask',
    'Appfuel\Kernel\Task\PHPAutoloaderTask',
    'Appfuel\Kernel\Task\FaultHandlerTask',
    'Appfuel\Kernel\Task\DependencyLoaderTask',
    // @todo validation startup task
);

/*
 * Allow the calling code to add tasks to the framework task list. You can 
 * append the extra tasks to the list (default), prepend the tasks or even 
 * the kernel tasks with your own.
 */
if (! isset($ctrl['task-action']) || ! is_string($ctrl['task-action'])) {
    $ctrl['task-action'] = 'append';
}

if (isset($ctrl['tasks']) && is_array($ctrl['tasks'])) {
    switch($taskAction) {
        case 'prepend':
            $tasks = array_merge($ctrl['tasks'], $tasks);
            break;
        case 'replace':
            $tasks = $fwTasks;
            break;
        default:
            $tasks = array_merge($tasks, $ctrl['tasks']);
    }
}

if (! isset($ctrl['app-type']) || ! is_string($ctrl['app-type'])) {
    $ctrl['app-type'] = 'web';
}

if ('web' === $ctrl['app-type']) {
    $handler = $factory->createWebHandler();
    if (! $handler instanceof WebHandlerInterface) {
        $class = gettype($handler);
        $err   = "Web app handler -($class) implement Appfuel\App\WebHandler";
        $err  .= "Interface";
        throw new LogicException($err);        
    } 
}
else {
    $handler = $factory->createConsoleHandler();
    if (! $handler instanceof ConsoleHandlerInterface) {
        $class = gettype($handler);
        $err   = "Console app handler -($class) must implement Appfuel\App";
        $err  .= "\ConsoleHandlerInterface";
        throw new LogicException($err);        
    } 
}
unset($detail, $config, $class);

/*
 * allow the calling code to opt out of initializing that handler
 */
if (isset($ctrl['disable-tasks']) && true === $ctrl['disable-tasks']) {
    return $handler;
}
$taskHandler->runTasks($tasks);
unset($tasks);

return $handler;
