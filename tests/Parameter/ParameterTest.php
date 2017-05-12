<?php

namespace webignition\Tests\InternetMediaType\Parameter;

use webignition\InternetMediaType\Parameter\Parameter;
use webignition\Tests\InternetMediaType\BaseTest;

class ParameterTest extends BaseTest
{
    /**
     * @dataProvider castToStringDataProvider
     *
     * @param string $attribute
     * @param string $value
     * @param string $expectedParameterString
     */
    public function testCastToString($attribute, $value, $expectedParameterString)
    {
        $parameter = new Parameter($attribute, $value);

        $this->assertEquals($expectedParameterString, (string)$parameter);
    }

    /**
     * @return array
     */
    public function castToStringDataProvider()
    {
        return [
            'all lowercase' => [
                'attribute' => 'attribute1',
                'value' => 'value1',
                'expectedOutput' => 'attribute1=value1'
            ],
            'attribute mixed case value lowercase' => [
                'attribute' => 'Attribute2',
                'value' => 'value2',
                'expectedOutput' => 'attribute2=value2'
            ],
            'attribute mixed case value uppercase' => [
                'attribute' => 'ATTribUTE3',
                'value' => 'VALUE3',
                'expectedOutput' => 'attribute3=VALUE3'
            ],
            'empty attribute' => [
                'attribute' => '',
                'value' => '{anything}',
                'expectedOutput' => ''
            ],
            'empty value' => [
                'attribute' => 'attributeValue',
                'value' => '',
                'expectedOutput' => 'attributevalue'
            ],
            'null value' => [
                'attribute' => 'attributeValue',
                'value' => null,
                'expectedOutput' => 'attributevalue'
            ],
        ];
    }
}
