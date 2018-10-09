<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\ValueParser;

class ValueParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueParser
     */
    private $parser;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->parser = new ValueParser();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param string $attribute
     * @param string $parameterString
     * @param string $expectedValue
     */
    public function testParse($attribute, $parameterString, $expectedValue)
    {
        $this->parser->setAttribute($attribute);
        $this->assertEquals($expectedValue, $this->parser->parse($parameterString));
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        return [
            [
                'attribute' => 'foo',
                'parameterString' => 'foo=bar',
                'expectedValue' => 'bar'
            ],            [
                'attribute' => 'foo',
                'parameterString' => 'foo="bar"',
                'expectedValue' => 'bar'
            ],
        ];
    }
}
