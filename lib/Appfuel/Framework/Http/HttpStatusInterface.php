<?php
/**
 * Appfuel
 * PHP 5.3+ object oriented MVC framework supporting domain driven design. 
 *
 * @package     Appfuel
 * @author      Robert Scott-Buccleuch <rsb.code@gmail.com.com>
 * @copyright   2009-2010 Robert Scott-Buccleuch <rsb.code@gmail.com>
 * @license		http://www.apache.org/licenses/LICENSE-2.0
 */
namespace Appfuel\Framework\Http;

/**
 * Defines functionality needed to use php header function
 */
interface HttpStatusInterface
{
	/**
	 * @return	string
	 */
	public function getCode();
	
	/**
	 * @return	bool
	 */
	public function getText();

	/**
	 * @return	string
	 */
	public function __toString();
}