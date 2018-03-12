<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeFixer;
use webignition\Tests\InternetMediaType\BaseTest;

class AttributeFixerTest extends BaseTest
{
    /**
     * @dataProvider fixValidAttributeDataProvider
     *
     * @param $attribute
     * @param $expectedFixedAttribute
     */
    public function testFixValidAttribute($attribute, $expectedFixedAttribute)
    {
        $attributeFixer = new AttributeFixer();
        $attributeFixer->setInputString($attribute);

        $this->assertEquals($expectedFixedAttribute, $attributeFixer->fix());
    }

    /**
     * @return array
     */
    public function fixValidAttributeDataProvider()
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
