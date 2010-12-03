<?php
/**
 * Appfuel
 * PHP object oriented MVC framework use to support developement with 
 * doman driven design.
 *
 * @package 	Appfuel
 * @author 		Robert Scott-Buccleuch <rob@rsbdev.com>
 * @copyright	2009-2010 Robert Scott-Buccleuch <rob@rsbdev.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 */
namespace 	Appfuel\StdLib\Config\Adapter;

use Appfuel\StdLib\Filesystem\File		as File;
use Appfuel\StdLib\Filesystem\Manager	as FileManager;

/**
 *
 * @package 	Appfuel
 */
class Ini implements AdapterInterface
{
	/**
	 * @return	AfList\Basic
	 */
	public function parse(File $file)
	{
		return FileManager::parseIni($file, TRUE);
	}
}
