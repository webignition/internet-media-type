<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\TypeFixer;

class TypeFixerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider fixSuccessDataProvider
     */
    public function testFixSuccess(
        string $internetMediaTypeString,
        int $invalidCharacterPosition,
        ?string $expectedType
    ) {
        $typeFixer = new TypeFixer();
        $typeFixer->setPosition($invalidCharacterPosition);
        $typeFixer->setInputString($internetMediaTypeString);

        $this->assertEquals($expectedType, $typeFixer->fix());
    }

    public function fixSuccessDataProvider(): array
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
     */
    public function testFixExceptionWhenParsing(string $internetMediaTypeString, int $invalidCharacterPosition)
    {
        $typeFixer = new TypeFixer();
        $typeFixer->setPosition($invalidCharacterPosition);
        $typeFixer->setInputString($internetMediaTypeString);

        $this->assertNull($typeFixer->fix());
    }

    public function fixExceptionWhenParsingDataProvider(): array
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
