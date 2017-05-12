<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\Parser;
use webignition\Tests\InternetMediaType\BaseTest;

class ParserTest extends BaseTest
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
        ];
    }
}
