<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\TypeParser;
use webignition\InternetMediaType\Parser\TypeParserException;
use webignition\Tests\InternetMediaType\BaseTest;

class TypeParserTest extends BaseTest
{
    /**
     *
     * @var TypeParser
     */
    private $parser;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->parser = new TypeParser();
    }

    /**
     * @dataProvider validTypeDataProvider
     *
     * @param string $internetMediaTypeString
     * @param string $expectedType
     */
    public function testParseValidType($internetMediaTypeString, $expectedType)
    {
        $this->assertEquals(
            $expectedType,
            $this->parser->parse($internetMediaTypeString)
        );
    }

    /**
     * @return array
     */
    public function validTypeDataProvider()
    {
        return [
            'type/subtype only' => [
                'internetMediaTypeString' => 'image/png',
                'expectedType' => 'image'
            ],
            'type/subtype and parameter' => [
                'internetMediaTypeString' => 'text/html charset=utf-8',
                'expectedType' => 'text'
            ],
        ];
    }

    public function testParseMalformedType()
    {
        $this->expectException(TypeParserException::class);
        $this->expectExceptionMessage('Invalid internal character after at position 1');
        $this->expectExceptionCode(1);

        $this->parser->parse('t e x t/html; charset=ISO-8859-4');
    }
}
