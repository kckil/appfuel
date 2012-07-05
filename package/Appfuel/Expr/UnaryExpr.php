<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Expr;

use InvalidArgumentException;

/**
 * Simple expression designed to old objects that support to string
 */
class UnaryExpr extends BasicExpr implements UnaryExprInterface
{
    /**
     * Operand used for this expression
     * @var string
     */
    protected $operand = null;

    /**
     * Operator used in the expression
     * @var string
     */
    protected $operator = null;

    /**
     * Determines if the expression is build as prexfix or postfix
     * @var string
     */
    protected $fixType = 'pre';
    
    /**
     * @param   string   $operand
     * @return  File
     */
    public function __construct($operator, $operand, $isParentheses = false)
    {
        $this->setOperator($operator);
        parent::__construct($operand, $isParentheses);
    }

    /**
     * @param    string    $type    pre|post
     * @return    null
     */
    public function setFixType($type)
    {
        $err = 'fix type must be (pre|post)';
        if (! is_string($type) || empty($type)) {
            throw new InvalidArgumentException(
                "$err param given is empty or not a string"
            );
        }

        if (! in_array($type, array('pre', 'post'))) {
            throw new InvalidArgumentException("$err param given is ($type)");
        }

        $this->fixType = $type;
    }

    /**
     * @return    string
     */
    public function getFixType()
    {
        return $this->fixType;    
    }

    /**
     * @return    bool
     */
    public function isPrefix()
    {
        return 'pre' === $this->fixType; 
    }

    /**
     * @return    bool
     */
    public function isPostfix()
    {
        return 'post' === $this->fixType; 
    }

    /**
     * @return    mixed string | object
     */
    public function getOperator()
    {
        return $this->operator;
    }
    
    /**
     * Build a postfix expression
     *
     * @return string
     */
    public function buildPostfix()
    {
        $operand = $this->getOperand();
        return  $this->convertToString($operand). ' ' . $this->getOperator();
    }

    /**
     * Build a prefix expression
     *
     * @return string
     */
    public function buildPrefix()
    {
        $operand = $this->getOperand();
        return  $this->getOperator() . ' ' . $this->convertToString($operand);
    }

    /**
     * @param    mixed    object | scalar
     * @return    null
     */
    protected function setOperator($op)
    {
        if (empty($op) || ! is_string($op)) {
            $err = 'Invalid operator must be a non empty string';
            throw new InvalidArgumentException($err);
        }

        $this->operator = $op;
    }

    /**
     * @return string
     */
    protected function doBuild()
    {
        if ($this->isPrefix()) {
            $str = $this->buildPrefix();
        } 
        else {
            $str = $this->buildPostfix();
        }

        return $str;
    }
}
