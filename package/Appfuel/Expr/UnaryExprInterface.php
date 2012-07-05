<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Expr;

interface UnaryExprInterface extends ExprInterface
{
    /**
     * Operator used in the urnary expression
     * @return    string
     */
    public function getOperator();

    /**
     * Used to deteremine which build to use
     * 
     * @param    string    $type
     * @return    null
     */
    public function setFixType($type);

    /**
     * @return string
     */
    public function getFixType();

    /**
     * @return bool
     */
    public function isPrefix();

    /**
     * @return bool
     */
    public function isPostfix();

    /**
     * Build expression a postfix
     * 
     * @return string
     */
    public function buildPostfix();
    
    /**
     * Build expression as prefix
     *
     * @return    string
     */
    public function buildPrefix();    
}
