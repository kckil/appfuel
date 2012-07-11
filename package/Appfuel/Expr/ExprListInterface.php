<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Expr;

use Countable,
    Iterator;
/**
 * Most basic of all expressions
 */
interface ExprListInterface extends Countable, Iterator
{
    /**
     * Returns an array of all expressions each item is an array with the
     * expression and logical operator that joins the next expression. The
     * last expression has no operator
     *
     * @return    array
     */
    public function getAll();
    

    /**
     * Add an expression to the list. Last expression has no logical operator.
     * The logical operator is always applied to the previous expression and
     * is ignored in the case of the first expressions
     *
     * @throws  Appfuel\Framework\Exception
     * @param   ExprInterface       $expr
     * @param   string              $logical    valid operators are (and|or)
     * @return  ExprListInterface
     */
    public function add(ExprInterface $expr, $logical = 'and');
    
}
