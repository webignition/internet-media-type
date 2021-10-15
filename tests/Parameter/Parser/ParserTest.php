<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\AttributeFixer;
use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\Parser;
use webignition\InternetMediaType\Parameter\Parser\ValueParser;
use webignition\InternetMediaType\Parser\Configuration;
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
            new AttributeFixer(),
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

    public function testSetGetConfiguration(): void
    {
        $configuration = new Configuration();

        $this->assertNotEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));

        $this->parser->setConfiguration($configuration);
        $this->assertEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));
    }

    /**
     * @dataProvider parseAndFixInvalidInternalCharacterDataProvider
     */
    public function testParseAndFixInvalidInternalCharacter(
        string $parameterString,
        string $expectedAttribute,
        string $expectedValue
    ): void {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();

        $parameter = $this->parser->parse($parameterString);

        $this->assertEquals($expectedAttribute, $parameter->getAttribute());
        $this->assertEquals($expectedValue, $parameter->getValue());
    }

    /**
     * @return array<mixed>
     */
    public function parseAndFixInvalidInternalCharacterDataProvider(): array
    {
        return [
            'charset: utf8' => [
                'parameterString' => 'charset: utf8',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'utf8',
            ],
        ];
    }
}
