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
 * base path is the absolute path to the application root, app path is the 
 * app directory which app setting, build files and files like this are kept,
 * it is the only directory at the root level that can not be changed. The 
 * package directory holds all the source files for the application.
 */
$sep   = DIRECTORY_SEPARATOR;
$base  = realpath(__DIR__ . '/../');
$app   = "$base{$sep}app";
$src   = "$base{$sep}package";

/*
 * Required constants. Appfuel will not work without these. Other contants are
 * declared towards the end of the script. They use the AppDetail which allows
 * you to change the directory names to suite your needs.
 */
define('AF_BASE_PATH', $base);
define('AF_APP_PATH', $app);
define('AF_CODE_PATH', $src);

/*
 * Load dependent framework files into memory before the autoloader.
 * This allows the framework tasks to be run earlier and not have to 
 * depend on the autoloader to be found.
 */ 
$file = "{$app}{$sep}kernel-dependencies.php";
if (! file_exists($file)) {
    $err = "could not find kernel dependency file at -($file)";
    throw new LogicException($err);
}
$dlist = require $file;

/*
 * Allow the calling script to append dependency classes along with the 
 * framework dependencies. The array given must be an associative array of 
 * qualified class name => file path (relative to AF_CODE_PATH)
 */
if (!isset($ctrl['depend-action']) || !is_string($ctrl['depend-action'])) {
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
 * Load dependencies into memory
 */
foreach ($dlist as $class => $file) {
    if (class_exists($class) || interface_exists($class, false)) {
        continue;    
    }
    $absolute = "{$src}{$sep}{$file}";
    if (! file_exists($absolute)) {
        $err = "could not find kernel dependency at -($absolute)";
        throw new RunTimeException($err);
    }

    require $absolute;
}
unset($file, $dlist, $dependList, $class, $asbsolute, $err);

/*
 * Decouple the application directories from the kernel. Note: the only 
 * directories you can not change are 'app' and 'app-build'. 
 */
if (! isset($ctrl['paths']) || ! is_array($ctrl['paths'])) {
    $ctrl['paths'] = array("base" => $base, "src" => "package");
}
$detail  = new AppDetail($ctrl['paths']);
$factory = new AppFactory();
$taskHandler = $factory->createTaskHandler();

/*
 * The detail and factory are needed globally so we set them to application 
 * registry where all globals for the app are kept
 */
AppRegistry::setAppDetail($detail);
AppRegistry::setAppFactory($factory);
AppRegistry::setTaskHandler($taskHandler);

$config = $factory->createConfigHandler();
/*
 * Allow the calling code to have control over configuration. By default if
 * the calling script populates a variable called $settings with an array
 * then that array will replace the original config settings.
 */
if (! isset($ctrl['config-action']) || ! is_string($ctrl['config-action'])) {
    $ctrl['config-action'] = 'replace';
}

/*
 * Allow the calling code to change the config file. Note: you must specify 
 * the absolute path to the config file you wish to load
 */
if (isset($ctrl['config-build-file'])) {
    $configFile = $ctrl['config-build-file'];
}
else {
    $configFile = $detail->getPath('config-build-settings');
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
    $headSettings = $settings;
}
else {
    $headSettings = $config->getFileData($configFile);
    if (null !== $settings) {
        $headSettings = array_merge($headSettings, $settings);
    }
}

/*
 * Load the configuration settings to the application registry.
 */
AppRegistry::load($headSettings);

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
