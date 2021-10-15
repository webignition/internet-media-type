<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\ValueParser;
use webignition\QuotedString\Parser as QuotedStringParser;

class ValueParserTest extends TestCase
{
    private ValueParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ValueParser(
            new QuotedStringParser()
        );
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $attribute, string $parameterString, string $expectedValue): void
    {
        $this->assertEquals($expectedValue, $this->parser->parse($parameterString, $attribute));
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            [
                'attribute' => 'foo',
                'parameterString' => 'foo=bar',
                'expectedValue' => 'bar'
            ],
            [
                'attribute' => 'foo',
                'parameterString' => 'foo="bar"',
                'expectedValue' => 'bar'
            ],
        ];
    }
}
