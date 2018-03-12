<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\TypeFixer;
use webignition\Tests\InternetMediaType\BaseTest;

class TypeFixerTest extends BaseTest
{
    /**
     * @dataProvider fixSuccessDataProvider
     *
     * @param string $internetMediaTypeString
     * @param int $invalidCharacterPosition
     * @param string $expectedType
     */
    public function testFixSuccess($internetMediaTypeString, $invalidCharacterPosition, $expectedType)
    {
        $typeFixer = new TypeFixer();
        $typeFixer->setPosition($invalidCharacterPosition);
        $typeFixer->setInputString($internetMediaTypeString);

        $this->assertEquals($expectedType, $typeFixer->fix());
    }

    /**
     * @return array
     */
    public function fixSuccessDataProvider()
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
            'type/subtype with parameters separated by space instead of semicolon; lacking space' => [
                'internetMediaTypeString' => 'text/htmlcharset=utf-8',
                'invalidCharacterPosition' => 9,
                'expectedType' => null,
            ],
            'empty' => [
                'internetMediaTypeString' => '',
                'invalidCharacterPosition' => 0,
                'expectedType' => null,
            ],
        ];
    }

    /**
     * @dataProvider fixExceptionWhenParsingDataProvider
     *
     * @param string $internetMediaTypeString
     * @param int $invalidCharacterPosition
     */
    public function testFixExceptionWhenParsing($internetMediaTypeString, $invalidCharacterPosition)
    {
        $typeFixer = new TypeFixer();
        $typeFixer->setPosition($invalidCharacterPosition);
        $typeFixer->setInputString($internetMediaTypeString);

        $this->assertNull($typeFixer->fix());
    }

    /**
     * @return array
     */
    public function fixExceptionWhenParsingDataProvider()
    {
        return [
            'TypeParserException; invalid internal character' => [
                'internetMediaTypeString' => 't ext/html, t ext/html',
                'invalidCharacterPosition' => 11,
            ],
            'SubTypeParserException; invalid internal character' => [
                'internetMediaTypeString' => 'text/h tml, text/h tml',
                'invalidCharacterPosition' => 11,
            ],
        ];
    }
}
