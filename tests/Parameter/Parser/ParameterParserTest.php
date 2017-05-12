<?php

namespace webignition\Tests\InternetMediaType;

use webignition\InternetMediaType\Parameter\Parser\Parser;
use webignition\QuotedString\QuotedString;

class ParameterParserTest extends BaseTest
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param $parameterString
     * @param $expectedAttribute
     * @param $expectedValue
     */
    public function testParse($parameterString, $expectedAttribute, $expectedValue)
    {
        $parameter = $this->parser->parse($parameterString);

        $this->assertEquals($expectedAttribute, $parameter->getAttribute());
        $this->assertEquals($expectedValue, $parameter->getValue());
    }

    /**
     * @return array
     */
    public function parseDataProvider()
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
