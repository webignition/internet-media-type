<?php

namespace webignition\Tests\InternetMediaType\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Exception\TypeParserException;
use webignition\InternetMediaType\Parser\TypeParser;

class TypeParserTest extends TestCase
{
    private TypeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new TypeParser();
    }

    /**
     * @dataProvider validTypeDataProvider
     */
    public function testParseValidType(string $internetMediaTypeString, string $expectedType): void
    {
        $this->assertEquals(
            $expectedType,
            $this->parser->parse($internetMediaTypeString)
        );
    }

    /**
     * @return array<mixed>
     */
    public function validTypeDataProvider(): array
    {
        return [
            'type/subtype only' => [
                'internetMediaTypeString' => 'image/png',
                'expectedType' => 'image'
            ],
            'type/subtype and parameter' => [
                'internetMediaTypeString' => 'text/html charset=utf-8',
                'expectedType' => 'text'
            ],
        ];
    }

    /**
     * @throws TypeParserException
     */
    public function testParseMalformedType(): void
    {
        $this->expectException(TypeParserException::class);
        $this->expectExceptionMessage('Invalid internal character after at position 1');
        $this->expectExceptionCode(1);

        $this->parser->parse('t e x t/html; charset=ISO-8859-4');
    }
}
