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
namespace Appfuel\Orm\Domain;

use Appfuel\Framework\Exception,
	Appfuel\Framework\Orm\Domain\DomainStateInterface;

/**
 * Holds information about the current state of the domain.
 */
class DomainState implements DomainStateInterface
{
	/**
	 * List of the initial member marshalled into the domain
	 * @var array
	 */
	protected $initialMembers = array();

	/**
	 * List of the members that have changed
	 * @var array
	 */
	protected $dirtyMembers = array();

	/**
	 * Current state of the domain
	 * @var string
	 */
	protected $state = 'new';

	/**
	 * List of valid states the domain can go through
	 * @var array
	 */
	protected $validStates = array('marshal', 'new', 'dirty', 'delete');

	/**
	 * @return	string
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param	string	$state
	 * @return	DomainState
	 */
	public function setState($state)
	{
		$err = "setState failed:";
		if (empty($state) || ! is_string($state)) {
			throw new Exception("$err param must be a non empty string");
		}

		if (! in_array($state, $this->validStates)) {
			$list = implode(',', $this->validStates);
			throw new Exception("$err param must be one of ($list)");
		}

		$this->state = $state;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function isMarshal()
	{
		return 'marshal' === $this->state;
	}

	/**
	 * @return bool
	 */
	public function isNew()
	{
		return 'new' === $this->state;
	}

	/**
	 * @return bool
	 */
	public function isDirty()
	{
		return 'dirty' === $this->state && ! empty($this->dirtyMembers);
	}

	/**
	 * @return bool
	 */
	public function isDelete()
	{
		return 'delete' === $this->state;
	}

	/**
	 * @param	array	$members 
	 * @return	DomainState
	 */
	public function markMarshal(array $members = null)
	{
		$this->setState('marshal');
		if (! empty($members)) {
			$this->initialMembers = $members;
		}

		return $this;
	}

	/**
	 * @param	string	$member		domain member that has changed
	 * @return	DomainState
	 */
	public function markDirty($member)
	{
		if ($this->isDirtyMember($member)) {
			return $this;
		}

		$this->dirtyMembers[] = $member;
		if (! $this->isDirty()) {
			$this->setState('dirty');
		}

		return $this;
	}

	/**
	 * @return DomainState
	 */
	public function markClean($member = null)
	{
		if (null === $member) {
			$this->setState('marshal');
			$this->dirtyMembers = array();
			return $this;
		}

		if ($this->isDirtyMember($member)) {
			$index = array_search($member, $this->dirtyMembers, true);
			if ($index !== false) {
				unset($this->dirtyMembers[$index]);
			}
		}

		return $this;
	}

	/**
	 * Remove any other status and mark this as new.
	 *
	 * @return	DomainState
	 */
	public function markNew()
	{
		$this->markClean();
		$this->setState('new');
		return $this;
	}
	
	/**
	 * Remove any other status and mark this as delete.
	 *
	 * @return	DomainState
	 */
	public function markDelete()
	{
		$this->markClean();
		$this->setState('delete');
		return $this;
	}
	
	/**
	 * @return	array
	 */
	public function getInitialMembers()
	{
		return $this->initialMembers;
	}

	/**
	 * @throws	Appfuel\Framework\Exception
	 * @param	string	$member
	 * @return	bool
	 */
	public function isDirtyMember($member)
	{
		if (empty($member) || ! is_string($member)) {
			throw new Exception("isDirtyMember failed param must be a string");
		}

		return in_array($member, $this->dirtyMembers);
	}

	/**
	 * @return	array
	 */
	public function getDirtyMembers()
	{
		return $this->dirtyMembers;
	}
}
