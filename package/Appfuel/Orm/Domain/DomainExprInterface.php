<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Orm\Domain;

use Appfuel\Expr\BinaryExprInterface;

/**
 * Domain Expressions are used by the repository to parse a single string
 * expressing a basic expression about a domain into a known datastructure.
 */
interface DomainExprInterface extends BinaryExprInterface
{
    /**
     * Returns the domain key used in the expression
     * @return    string
     */
    public function getDomain();

    /**
     * Returns the domain member used in the expression
     * @return string
     */
    public function getMember();

    /**
     * Returns the value of the expression
     * @return string
     */
    public function getValue();

}
