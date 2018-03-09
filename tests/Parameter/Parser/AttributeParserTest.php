<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\Tests\InternetMediaType\BaseTest;

class AttributeParserTest extends BaseTest
{
    /**
     * @var AttributeParser
     */
    private $parser;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();
        $this->parser = new AttributeParser();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param string $attribute
     * @param string $expectedName
     */
    public function testParse($attribute, $expectedName)
    {
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        return [
            'foo=bar' => [
                'attribute' => 'foo=bar',
                'expectedName' => 'foo',
            ],
            'charset=ISO-8859-4' => [
                'attribute' => 'charset=ISO-8859-4',
                'expectedName' => 'charset',
            ],
            'charset' => [
                'attribute' => 'charset',
                'expectedName' => 'charset',
            ],
            'charset=' => [
                'attribute' => 'charset=',
                'expectedName' => 'charset',
            ],
        ];
    }

    /**
     * @dataProvider parseInvalidInternalCharacterDataProvider
     *
     * @param string $attribute
     * @param int $expectedInvalidInternalCharacterPosition
     */
    public function testParseInvalidInternalCharacter($attribute, $expectedInvalidInternalCharacterPosition)
    {
        $this->expectException(AttributeParserException::class);
        $this->expectExceptionMessage(
            'Invalid internal character after at position ' . $expectedInvalidInternalCharacterPosition
        );
        $this->expectExceptionCode(1);

        $this->parser->parse($attribute);
    }

    /**
     * @return array
     */
    public function parseInvalidInternalCharacterDataProvider()
    {
        return [
            'ch arset=ISO-8859-4' => [
                'attribute' => 'ch arset=ISO-8859-4',
                'expectedInvalidInternalCharacterPosition' => 2
            ],
        ];
    }

    /**
     * @dataProvider parseAndFixInvalidInternalCharacterDataProvider
     *
     * @param string $attribute
     */
    public function testParseAndFixInvalidInternalCharacter($attribute)
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->parser->parse($attribute);
    }

    /**
     * @return array
     */
    public function parseAndFixInvalidInternalCharacterDataProvider()
    {
        return [
            'charset: utf8' => [
                'attribute' => 'charset',
            ],
        ];
    }
}
