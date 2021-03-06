<?php

use PHPUnit\Framework\TestCase;

use Fleks\Db\Query\Func;

class FuncTest extends TestCase
{
    public function testReturnsLiteral()
    {
        // The expression to test
        $expression = "literal expression";

        // Create
        $func = new Func($expression);

        // Assert
        $this->assertEquals($expression, (string)$func);
    }
}
