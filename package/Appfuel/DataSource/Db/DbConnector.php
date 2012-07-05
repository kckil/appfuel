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

use LogicException;

/**
 * The connector hides the details of handling the connection objects. In
 * some sytems namely replication systems we need to know if a request is
 * serviced via a master connection for all writes or slave connection for 
 * all reads. This is model with a system like HA proxy  in mind where we need
 * only to know about one master and one slave. When replication is not used
 * then the master will transparently be used to all requests.
 */
class DbConnector implements DbConnectorInterface
{
	/**
	 * @var	DbConnInterface
	 */
	protected $master = null;
	
	/**
	 * @var DbConnInterface
	 */
	protected $slave = null;

	/**
	 * @param	DbConnInterface	$master
	 * @param	DbConnInterface	$slave	optional 
	 * @return	DbConnector
	 */
	public function __construct(DbConnInterface $master,
								DbConnInterface $slave = null)
	{
		$this->master = $master;

		if (null !== $slave) {
			$this->slave  = $slave;
		}
	}

	/**
	 * When no slave exists a master is always returns. 
	 * When any type other than read is given then master is returned
	 * When type is read and a slave exists then the slave is given
	 *
	 * @param	string	$type	read forces the slave connection
	 * @return	DbConnInterface | null if shutdown
	 */
	public function getConnection($type = 'write')
	{
		if (! $this->isSlave() || 'read' !== $type) {
			return $this->getMaster();
		}
	
		return $this->getSlave();
	}

	/**
	 * @return	ConnectionInterface | null
	 */
	public function getMaster()
	{
		return $this->master;
	}

	/**
	 * @return	bool
	 */
	public function isMaster()
	{
		return $this->master instanceof DbConnInterface;
	}

	/**
	 * @return	ConnectionInterface
	 */
	public function getSlave()
	{
		return $this->slave;
	}

	/**
	 * @return	bool
	 */
	public function isSlave()
	{
		return $this->slave instanceof DbConnInterface;
	}

	/**
	 * @return null
	 */
	public function shutdown($conn = null)
	{
		if ('master' === $conn || null === $conn) {
			$this->shutdownMaster();
		}

		if ('slave' === $conn || null === $conn) {
			$this->shutdownSlave();
		}
	}

	/**
	 * @throws	LogicException
	 */
	public function __clone()
	{
		throw new LogicException("Clone is not allowed for Connectors");
	}

	/**
	 * @throws	LogicException
	 */
	public function __wakeup()
	{
		throw new LogicException("Unserializing is not allowed for Connectors");
	}

	/**
	 * @return	null
	 */
	protected function shutdownMaster()
	{
		if ($this->isMaster()) {
			$this->master->close();
			$this->master = null;
		}
	}

	/**
	 * @return	null
	 */
	protected function shutdownSlave()
	{
		if ($this->isSlave()) {
			$this->slave->close();
			$this->slave = null;
		}
	}
}
