<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\TypeFixer;
use webignition\Tests\InternetMediaType\BaseTest;

class TypeFixerTest extends BaseTest
{
    /**
     * @dataProvider fixDataProvider
     *
     * @param string $internetMediaTypeString
     * @param int $invalidCharacterPosition
     * @param string $expectedType
     */
    public function testFix($internetMediaTypeString, $invalidCharacterPosition, $expectedType)
    {
        $typeFixer = new TypeFixer();
        $typeFixer->setPosition($invalidCharacterPosition);
        $typeFixer->setInputString($internetMediaTypeString);

        $this->assertEquals($expectedType, $typeFixer->fix());
    }

    /**
     * @return array
     */
    public function fixDataProvider()
    {
        return [
            'type/subtype doubled and comma-separated' => [
                'internetMediaTypeString' => 'text/html, text/html',
                'invalidCharacterPosition' => 10,
                'expectedType' => 'text/html',
            ],
            'type/subtype with parameters separated by space instead of semicolon' => [
                'internetMediaTypeString' => 'text/html charset=utf-8',
                'invalidCharacterPosition' => 9,
                'expectedType' => 'text/html',
            ],
        ];
    }
}
