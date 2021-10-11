<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Tests\InternetMediaType\Parameter;

use webignition\InternetMediaType\Parameter\Parameter;

class ParameterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider castToStringDataProvider
     */
    public function testCastToString(string $attribute, ?string $value, string $expectedParameterString): void
    {
        $parameter = new Parameter($attribute, $value);

        $this->assertEquals($expectedParameterString, (string)$parameter);
    }

    /**
     * @return array<mixed>
     */
    public function castToStringDataProvider(): array
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
