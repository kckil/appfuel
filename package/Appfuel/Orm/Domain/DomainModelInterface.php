<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\Domain;

/**
 * Functionality needed to function as a domain
 */
interface DomainModelInterface
{
    /**
     * @return  scalar
     */
    public function getId();

    /**
     * @param   scalar  $id
     * @return  DomainModelInterface
     */
    public function setId($id);

    /**
     * Marshalling is acting of building a domain from the datasource. Every
     * domain has the ability internally marshal already mapped data into its
     * member variables. This strict flag determines if the domain will throw
     * an exception or not when the member does not exist
     * @return bool
     */
    public function isStrictMarshalling();

    /**
     * @return  DomainModel
     */
    public function enableStrictMarshalling();

    /**
     * @return  DomainModel
     */
    public function disableStrictMarshalling();
    
    /**
     * Marshal the datasource values into the domain members and updata the 
     * state object
     *
     * @param   array   $data   member name names and values 
     * @return  DomainModel
     */
    public function marshal(array $data = null);

    /**
     * Used to indicate that a member attribute has changed 
     * 
     * @param   string    $member        name of the domain attr thats changed
     * @return  bool
     */
    public function markDirty($member);

    /**
     * Used to remove a single member or all members from being marked dirty.
     * When $member is null all members should be marked clean
     *
     * @param   string    $member     domain attr to mark clean. 
     * @return  bool
     */
    public function markClean($member = null);

    /**
     * Used to indicate that the domain state is new
     * @return  null
     */
    public function markNew();

    /**
     * Used to indicate that the domain is in a state of deletion
     * 
     * @return    null
     */
    public function markDelete();

    /**
     * Determines that state of the damain
     * 
     * @return    DomainState
     */
    public function getDomainState();
    
    /**
     * @param    DomainState $state
     * @return    DomainModel
     */
    public function setDomainState(DomainStateInterface $state);
}
