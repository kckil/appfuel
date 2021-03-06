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
namespace Appfuel\DataSource\Db;

use RunTimeException,
	InvalidArgumentException,
	Appfuel\DataStructure\Dictionary,
	Appfuel\DataStructure\DictionaryInterface;

/**
 * Holds two types of data, raw connection parameters, and database connectors.
 * Raw connection parameters are a list of name/value pairs held in an 
 * DictionaryInterface. They are used by DbConnInterface objects that are db 
 * vendor specific objects used to connect to the db server.
 * DbConnectorInterface objects are db vendor agnostic objects that encapulate
 * DbConnInterfaces for master/slave replication systems or single db servers.
 * There are two separate interfaces for getting connectors and parameters, 
 * because they share the same key
 */
class DbRegistry
{
	/**
	 * The default connector key
	 * @var string
	 */
	static protected $defaultKey = null;

	/**
	 * List of DictionaryInterfaces identified by a label
	 * @var array
	 */
	static protected $params = array();

	/**
	 * List of DbConnectorInterfaces 
	 * @var array
	 */
	static protected $conns = array();

	/**
	 * @return	string
	 */
	static public function getDefaultConnectorKey()
	{
		return self::$defaultKey;
	}

	/**
	 * @param	string	$key
	 * @return	null
	 */
	static public function setDefaultConnectorKey($key)
	{
		if (! is_string($key) || empty($key)) {
			$err = 'default connector key must be a non empty string';
			throw new InvalidArgumentException($err);
		}

		self::$defaultKey = $key;
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isConnector($key)
	{
		if (is_string($key) && isset(self::$conns[$key])) {
			return true;
		}

		return false;	
	}

	/**
	 * @return	array
	 */
	static public function getAllConnectors()
	{
		return self::$conns;
	}

	/**
	 * @param	string	$key
	 * @return	DictionaryInterface | false 
	 */
	static public function getConnector($key)
	{
		if (! self::isConnector($key)) {
			return false;
		}

		return self::$conns[$key];
	}

	/**
	 * @param	string	$key
	 * @param	mixed	array | DictionaryInterface	 $params
	 * @return	null
	 */
	static public function addConnector($key, DbConnectorInterface $conn)
	{
		if (! is_string($key)) {
			$err = 'connector key must be a string';
			throw new InvalidArgumentException($err);
		}

		self::$conns[$key] = $conn;
	}
	
	/**
	 * @params	array	$list
	 * @return	null
	 */
	static public function loadConnectors(array $list)
	{
		if ($list === array_values($list)) {
			$err  = 'list of connectors must be an associative ';
			$err .= 'of named connectors';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $key => $connector) {
			self::addConnector($key, $connector);
		}
	}

	/**
	 * @params	array	$list
	 * @return	null
	 */
	static public function setConnectors(array $list)
	{
		self::clear();
		self::loadConnectors($list);		
	}

	/**
	 * @return	null
	 */
	static public function clearConnectors()
	{
		self::$conns = array();
	}

	/**
	 * @return	null
	 */
	static public function clear()
	{
		self::clearConnectors();
		self::clearConnectionParams();
	}

	/**
	 * @param	string	$key
	 * @return	bool
	 */
	static public function isConnectionParams($key)
	{
		if (is_string($key) && isset(self::$params[$key])) {
			return true;
		}

		return false;
	}

	/**
	 * @return	array
	 */
	static public function getAllConnectionParams()
	{
		return self::$params;
	}

	/**
	 * @param	string	$key
	 * @return	DictionaryInterface | false 
	 */
	static public function getConnectionParams($key)
	{
		if (! self::isConnectionParams($key)) {
			return false;
		}

		return self::$params[$key];
	}

	/**
	 * @param	string	$key
	 * @param	mixed	array | DictionaryInterface	 $params
	 * @return	null
	 */
	static public function addConnectionParams($key, $params)
	{
		if (! is_string($key)) {
			$err = 'connection parameter key must be a string';
			throw new InvalidArgumentException($err);
		}

        if (is_array($params)) {
            $params = new Dictionary($params);
        }
        else if (! ($params instanceof DictionaryInterface)) {
            $err  = 'db connection parameters must be either an array ';
            $err .= 'or an object that implements Appfuel\DataStructure';
            $err .= '\DictionaryInterface';
            throw new InvalidArgumentException($err);
        }

		self::$params[$key] = $params;
	}

	/**
	 * @param	array	$list	list of named database connection params
	 * @return	null
	 */
	static public function loadConnectionParams(array $list)
	{
		if ($list === array_values($list)) {
			$err  = 'list of connection parameters must be an associative ';
			$err .= 'of named parameters';
			throw new InvalidArgumentException($err);
		}

		foreach ($list as $key => $params) {
			self::addConnectionParams($key, $params);
		}
	}

	/**
	 * @param	array	$list	list of named database connection params
	 * @return	null
	 */
	static public function setConnectionParams(array $list)
	{
		self::clearConnectionParams();
		self::loadConnectionParams($list);
	}

	/**
	 * @return	null
	 */
	static public function clearConnectionParams()
	{
		self::$params = array();
	}
}
