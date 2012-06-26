<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
use Appfuel\App\AppDetail,
    Appfuel\App\AppFactory,
    Appfuel\App\ConfigHandler;

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
if (!isset($dependAction) || !is_string($dependAction)) {
    $dependAction = 'append';
}
if (isset($dependList) && is_array($dependList)) {
    if ($dependList === array_values($dependList)) {
        $err  = "dependency array given by the calling script must be an ";
        $err .= "associative array";
        throw new LogicException($err);
    }
    
    switch ($dependAction) {
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
unset($file, $dlist, $class, $asbsolute, $err);

/*
 * Decouple the application directories from the kernel. Note: the only 
 * directories you can not change are 'app' and 'app-build'. 
 */
$paths  = ["base" => $base, "src" => "package"];
$detail = new AppDetail($paths);

$factory = new AppFactory();
$config  = $factory->createConfigHandler();

/*
 * Allow the calling code to have control over configuration. By default if
 * the calling script populates a variable called $settings with an array
 * then that array will replace the original config settings.
 */
if (!isset($configAction) || !is_string($configAction)) {
    $configAction = 'replace';
}

/*
 * Allow the calling code to change the config file. Note: you must specify 
 * the absolute path to the config file you wish to load
 */
if (! isset($configFile)) {
    $configFile = $detail->getPath('config-build-settings');
}

/*
 * Ignore normal config data when calling scripts is using its own otherwise
 * merge the calling scripts config settings with ours
 */
if (isset($settings) && 'replace' === $configAction) {
    $headSettings = $settings;
}
else {
    $headSettings = $config->getFileData($configFile);
    if (isset($settings) && is_array($settings)) {
        $headSettings = array_merge($headSettings, $settings);
    }
}

/*
 * Add the configuration settings to the application registry. Note: this 
 * clears out any settings already there. Use loadRegistry to append if you
 * need to
 */
$config->setRegistry($headSettings);

/*
 * list of framework startup tasks to be run after initialization. The 
 * including script can append, prepend or replaces these when needed. 
 */
$tasks = array(
    'Appfuel\Kernel\PHPIniTask',
    'Appfuel\Kernel\PHPErrorTask',
    'Appfuel\Kernel\PHPPathTask',
    'Appfuel\Kernel\PHPDefaultTimezoneTask',
    'Appfuel\Kernel\PHPAutoloaderTask',
    'Appfuel\Kernel\FaultHandlerTask',
    'Appfuel\Kernel\DependencyLoaderTask',
    'Appfuel\Kernel\RouteListTask',
    'Appfuel\Validate\ValidationStartupTask'
);

/*
 * Allow the calling code to add tasks to the framework task list. You can 
 * append the extra tasks to the list (default), prepend the tasks or even 
 * the kernel tasks with your own.
 */
if (! isset($taskAction) || ! is_string($taskAction)) {
    $taskAction = 'append';
}

if (isset($fwTasks) && is_array($fwTasks)) {
    switch($taskAction) {
        case 'prepend':
            $tasks = array_merge($fwTasks, $tasks);
            break;
        case 'replace':
            $tasks = $fwTasks;
            break;
        default:
            $tasks = array_merge($tasks, $fwTasks);
    }
}

if (isset($appType) && 'cli' === $appType) {
    $handler = new CliHandler($detail, $factory);
}
else {
    $handler = new WebHandler($detail, $factory);
}

/*
 * allow the calling code to opt out of initializing that handler
 */
if (isset($disableInitialize) && true === $disableInitialize) {
    unset($tasks, $detail, $config);
    return $handler;
}

$handler->initialize($tasks);
unset($tasks, $detail, $config);

return $handler;
