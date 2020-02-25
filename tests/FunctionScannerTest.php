<?php

use honray\FunctionBuilder;
use honray\FunctionScanner;
use honray\WKFunctions;
use PHPUnit\Framework\TestCase;

class FunctionScannerTest extends TestCase
{
    public function testScanFolder()
    {
        $builder = $this->createMock(FunctionBuilder::class);

        $builder->method('build')
            ->willReturn(
                new WKFunctions(__DIR__ . '/test_functions/TestOne.php')
            );

        $functionScanner = new FunctionScanner($builder);
        $result = $functionScanner->scanFolder('./');

        $this->assertNotEmpty($result);
    }
}