<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
 
/*
 * setup include paths so that we can autoload. This bootstrap script
 * has positional importance and thus should not be moved. The two
 * paths currently assumed are the appfuel and the test dir path. We 
 * also keep the original include path otherwise we need to include 
 * phpunit files
 */
$tDir  = dirname(__FILE__);
$afDir = realpath($tDir . '/../');

$afDirLib = $afDir . DIRECTORY_SEPARATOR . 'lib';
if (! defined('TEST_AF_BASE_PATH')) {
	define('TEST_AF_BASE_PATH', $afDir);
}

define ('AF_TEST_PATH', $tDir);
define ('AF_TEST_EXAMPLE_PATH', $tDir . DIRECTORY_SEPARATOR . 'example');

$managerFile = $afDirLib   . DIRECTORY_SEPARATOR .
			  'Appfuel'    . DIRECTORY_SEPARATOR .
			  'AppManager.php';

require_once $managerFile;

$configFile = 'test'   . DIRECTORY_SEPARATOR . 
			  'config' . DIRECTORY_SEPARATOR .
			  'test.ini';
 
\Appfuel\AppManager::init($afDir, $configFile);
