<?php

namespace webignition\Tests\InternetMediaType\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Exception\AttributeParserException;
use webignition\InternetMediaType\Parser\AttributeParser;

class AttributeParserTest extends TestCase
{
    private AttributeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new AttributeParser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $attribute, string $expectedName): void
    {
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'foo=bar' => [
                'attribute' => 'foo=bar',
                'expectedName' => 'foo',
            ],
            'charset=ISO-8859-4' => [
                'attribute' => 'charset=ISO-8859-4',
                'expectedName' => 'charset',
            ],
            'charset' => [
                'attribute' => 'charset',
                'expectedName' => 'charset',
            ],
            'charset=' => [
                'attribute' => 'charset=',
                'expectedName' => 'charset',
            ],
        ];
    }

    /**
     * @dataProvider parseInvalidInternalCharacterDataProvider
     */
    public function testParseInvalidInternalCharacter(
        string $attribute,
        int $expectedInvalidInternalCharacterPosition
    ): void {
        $this->expectException(AttributeParserException::class);
        $this->expectExceptionMessage(
            'Invalid internal character after at position ' . $expectedInvalidInternalCharacterPosition
        );
        $this->expectExceptionCode(1);

        $this->parser->parse($attribute);
    }

    /**
     * @return array<mixed>
     */
    public function parseInvalidInternalCharacterDataProvider(): array
    {
        return [
            'ch arset=ISO-8859-4' => [
                'attribute' => 'ch ar set=ISO-8859-4',
                'expectedInvalidInternalCharacterPosition' => 2
            ],
        ];
    }

    public function testParseInvalidAttribute(): void
    {
        self::expectExceptionObject(new AttributeParserException(
            'Invalid internal character after at position 7',
            1,
            7
        ));

        $this->parser->parse('charset"foo": utf8');
    }
}
