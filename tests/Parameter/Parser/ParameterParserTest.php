<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parameter\Parser\Parser;

class ParameterParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $parameterString, string $expectedAttribute, string $expectedValue)
    {
        $parameter = $this->parser->parse($parameterString);

        $this->assertEquals($expectedAttribute, $parameter->getAttribute());
        $this->assertEquals($expectedValue, $parameter->getValue());
    }

    public function parseDataProvider(): array
    {
        return [
            'charset=ISO-8859-4' => [
                'parameterString' => 'charset=ISO-8859-4',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'ISO-8859-4',
            ],
            'foo=bar' => [
                'parameterString' => 'foo=bar',
                'expectedAttribute' => 'foo',
                'expectedValue' => 'bar',
            ],
            'charset="ISO-8859-4"' => [
                'parameterString' => 'charset="ISO-8859-4"',
                'expectedAttribute' => 'charset',
                'expectedValue' => 'ISO-8859-4',
            ],
            'foo="bar"' => [
                'parameterString' => 'foo="bar"',
                'expectedAttribute' => 'foo',
                'expectedValue' => 'bar',
            ],
        ];
    }
}
