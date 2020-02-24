<?php

namespace honary\tests;

use PHPUnit\Framework\TestCase;
use honray\WKFunctions;

class WKFunctionsTest extends TestCase
{
    /**
     * 测试getFunctionData
     *
     * @dataProvider phpFileProvider
     * @return void
     */
    public function testGetFunctionData($path, $result)
    {
        $wkFunctions = new WKFunctions($path);

        $funcData = $wkFunctions->getFunctionData();

        $this->assertEquals($result, $funcData);
    }

    public function phpFileProvider()
    {
        return [
            [   
                __DIR__ . '/test_functions/TestOne.php', 
                [
                    'title' => '测试类1',
                    'pakege_name' => 'honray\TestOne',
                    'functions' => [
                        [
                            'title' => '测试函数1',
                            'name' => 'testFunOne',
                            'param' => [
                                [
                                    'type' => 'integer',
                                    'name' => 'paramA',
                                    'title' => '参数A'
                                ],
                                [
                                    'type' => 'integer',
                                    'name' => 'paramB',
                                    'title' => '参数B'
                                ]
                            ],
                            'return_param' => [
                                [
                                    'type' => 'array',
                                    'name' => 'result',
                                    'title' => null
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [   
                __DIR__ . '/test_functions/TestTwo.php', 
                null
            ],
            [   
                __DIR__ . '/test_functions/TestThree.php', 
                [
                    'title' => 'TestThree',
                    'pakege_name' => 'honray\TestThree',
                    'functions' => [
                        [
                            'title' => 'funcA',
                            'name' => 'funcA',
                            'param' => [
                                [
                                    'type' => '',
                                    'name' => 'paramA',
                                    'title' => 'paramA'
                                ]
                            ],
                            'return_param' => [
                                [
                                    'type' => 'void',
                                    'name' => '',
                                    'title' => ''
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}