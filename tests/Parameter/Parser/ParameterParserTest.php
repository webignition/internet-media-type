<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parameter\Parser\Parser;

class ParameterParserTest extends \PHPUnit\Framework\TestCase
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
     *
     * @throws AttributeParserException
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
