<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\Parser;
use webignition\InternetMediaType\Parameter\Parser\ValueParser;
use webignition\QuotedString\Parser as QuotedStringParser;

class ParserTest extends TestCase
{
    private Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new Parser(
            new AttributeParser(),
            new ValueParser(new QuotedStringParser()),
        );
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $parameterString, string $expectedAttribute, ?string $expectedValue): void
    {
        $parameter = $this->parser->parse($parameterString);

        $this->assertEquals($expectedAttribute, $parameter->getAttribute());
        $this->assertEquals($expectedValue, $parameter->getValue());
    }

    /**
     * @return array<mixed>
     */
    public function parseDataProvider(): array
    {
        return [
            'empty' => [
                'parameterString' => '',
                'expectedAttribute' => '',
                'expectedValue' => '',
            ],
            'charset=ISO-8859-4' => [
                'parameterString' => 'charset=ISO-8859-4',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'ISO-8859-4',
            ],
            'charset=utf-8' => [
                'parameterString' => 'charset=utf-8',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'utf-8',
            ],
            'charset="ISO-8859-4"' => [
                'parameterString' => 'charset="ISO-8859-4"',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'ISO-8859-4',
            ],
            'charset="utf-8"' => [
                'parameterString' => 'charset="utf-8"',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'utf-8',
            ],
            'foo' => [
                'parameterString' => 'foo',
                'expectedAttribute' => 'foo',
                'expectedValue' => null,
            ],
            'foo=bar' => [
                'parameterString' => 'foo=bar',
                'expectedAttribute' => 'foo',
                'expectedValue' => 'bar',
            ],
            'foo="bar"' => [
                'parameterString' => 'foo="bar"',
                'expectedAttribute' => 'foo',
                'expectedValue' => 'bar',
            ],
        ];
    }
}
