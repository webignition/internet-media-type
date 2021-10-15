<?php

namespace webignition\Tests\InternetMediaType\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parser\SubtypeParser;
use webignition\InternetMediaType\Parser\SubtypeParserException;

class SubtypeParserTest extends TestCase
{
    private SubtypeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new SubtypeParser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $internetMediaTypeString, string $expectedSubtype): void
    {
        $this->assertEquals(
            $expectedSubtype,
            $this->parser->parse($internetMediaTypeString)
        );
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'without parameters' => [
                'internetMediaTypeString' => 'image/png',
                'expectedSubtype' => 'png',
            ],
            'with parameters' => [
                'internetMediaTypeString' => 'text/html; charset=utf-8',
                'expectedSubtype' => 'html',
            ],
        ];
    }

    /**
     * @throws SubtypeParserException
     */
    public function testParseInvalidSubtype(): void
    {
        $this->expectException(SubtypeParserException::class);
        $this->expectExceptionMessage('Invalid internal character after at position 6');
        $this->expectExceptionCode(1);

        $this->parser->parse('text/h t m l; charset=ISO-8859-4');
    }
}
