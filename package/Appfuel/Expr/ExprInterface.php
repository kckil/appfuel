<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Expr;

interface ExprInterface
{
    /**
     * @return    mixed    string | object
     */
    public function getOperand();

    /**
     * Turns the expression into a string
     *
     * @return    string
     */
    public function build();

    /**
     * @return  BasicExpr
     */
    public function enableParentheses();

    /**
     * @return  BasicExpr
     */
    public function disableParentheses();

    /**
     * Accounts for programatically setting the status when readablity is not
     * an issue
     *
     * @param    bool    $flag
     * @return    BasicExpr
     */
    public function setParenthesesStatus($flag);

    /**
     * Flag used to determine if the expr will be wrapped in parentheses
     * @return  BasicExpr
     */
    public function isParentheses();
    
    /**
     * magic method to allow expressions to exist in the context of a string
     *
     * @return string
     */
    public function __toString();
}
