<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\DbSource;

use InvalidArgumentException;

/**
 * The orm map is used to map database
 */
interface DbTableMapInterface
{
    /**
     * @return    string
     */
    public function getTableName();

    /**
     * @return    array
     */
    public function getColumnMap();

    /**
     * @return    return    string
     */
    public function getTableAlias();

    /**
     * @param    string    $member
     * @return    string | false when not found
     */
    public function mapColumn($member, $isAlias = false);

    /**
     * @param    string    $member
     * @return    string | false when not found
     */
    public function mapMember($column);

    /**
     * @return    array
     */
    public function getAllColumns($isAlias = false);

    /**
     * @return    array
     */
    public function getAllMembers();
}
