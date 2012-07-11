<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\Domain;

use Countable,
    Iterator;
/**
 * Domain collections can hold one or more domains of a single type
 */
interface DomainCollectionInterface extends Countable, Iterator
{
    /**
     * Used to enforce that only domains of this type will be allowed in
     * the collection
     * 
     * @return    string
     */
    public function getDomainKey();

    /**
     * Used to build domain objects from raw data
     * 
     * @return    DomainBuilderInterface
     */
    public function getDomainBuilder();

    /**
     * Used to load a collection as a raw dataset ready to be lazy loaded
     * when it is requested
     * 
     * @param    array
     * @return    DomainCollectionInterface
     */
    public function loadRawData(array $data);

    /**
     * Adds a domain into this collection. It should be noted that 
     * the domain keeps track of its own state so the collection need not
     * worry about such issues.
     * 
     * @param    DomainModelInterface
     * @return    DomainCollectionInterface
     */    
    public function add(DomainModelInterface $domain);
}
