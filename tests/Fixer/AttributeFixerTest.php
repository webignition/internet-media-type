<?php

namespace webignition\Tests\InternetMediaType\Fixer;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Fixer\AttributeFixer;

class AttributeFixerTest extends TestCase
{
    /**
     * @dataProvider fixValidAttributeDataProvider
     */
    public function testFixValidAttribute(string $attribute, string $expectedFixedAttribute): void
    {
        $attributeFixer = new AttributeFixer();

        $this->assertEquals($expectedFixedAttribute, $attributeFixer->fix($attribute));
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
