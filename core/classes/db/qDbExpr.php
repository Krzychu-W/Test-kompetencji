<?php

namespace qDbPdo;

/**
 * Class Expr.
 */
class Expr
{
    /** @var string wyrażenie */
    protected $_expression;

    /**
     * SqlExpr constructor.
     *
     * @param $expression
     */
    public function __construct($expression)
    {
        $this->_expression = (string) $expression;
    }

    /**
     * serializer.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_expression;
    }
}
