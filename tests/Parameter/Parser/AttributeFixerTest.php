<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\AttributeFixer;

class AttributeFixerTest extends TestCase
{
    /**
     * @dataProvider fixValidAttributeDataProvider
     */
    public function testFixValidAttribute(string $attribute, string $expectedFixedAttribute): void
    {
        $attributeFixer = new AttributeFixer();
        $attributeFixer->setInputString($attribute);

        $this->assertEquals($expectedFixedAttribute, $attributeFixer->fix());
    }

    /**
     * @return array<mixed>
     */
    public function fixValidAttributeDataProvider(): array
    {
        return [
            'valid empty attribute' => [
                'attribute' => '',
                'expectedFixedAttribute' => '',
            ],
            'valid non-empty attribute' => [
                'attribute' => 'foo=bar',
                'expectedFixedAttribute' => 'foo=bar',
            ],
            'invalid attribute with colon instead of equals with space' => [
                'attribute' => 'foo: bar',
                'expectedFixedAttribute' => 'foo=bar',
            ],
            'invalid, unfixable' => [
                'attribute' => 'foo"bar"',
                'expectedFixedAttribute' => 'foo"bar"',
            ],
        ];
    }
}
