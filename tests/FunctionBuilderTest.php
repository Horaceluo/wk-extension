<?php

namespace honray\tests;

use honray\FunctionBuilder;
use honray\WKFunctions;
use PHPUnit\Framework\TestCase;

class FunctionBuilderTest extends TestCase
{
    public function testBuild()
    {
        $wkFunction = (new FunctionBuilder())->build('test_functions/TestOne.php');

        $this->assertTrue($wkFunction instanceof WKFunctions);
    }
}