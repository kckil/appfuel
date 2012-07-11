<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Expr;

interface BinaryExprInterface extends ExprInterface
{
    /**
     * Operator used in the urnary expression
     * @return    string
     */
    public function getOperator();

    /**
     * @return string | object
     */
    public function getLeftOperand();
    
    /**
     * @return    string    | object
     */
    public function getRightOperand();
}
