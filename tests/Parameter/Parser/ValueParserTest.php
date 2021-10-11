<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\ValueParser;

class ValueParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ValueParser
     */
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ValueParser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $attribute, string $parameterString, string $expectedValue)
    {
        $this->parser->setAttribute($attribute);
        $this->assertEquals($expectedValue, $this->parser->parse($parameterString));
    }

    public function parseDataProvider(): array
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
