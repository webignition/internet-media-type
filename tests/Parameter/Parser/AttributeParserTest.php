<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;

class AttributeParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AttributeParser
     */
    private $parser;

    public function setUp()
    {
        parent::setUp();
        $this->parser = new AttributeParser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $attribute, string $expectedName)
    {
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    public function parseDataProvider(): array
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
     */
    public function testParseInvalidInternalCharacter(string $attribute, int $expectedInvalidInternalCharacterPosition)
    {
        $this->expectException(AttributeParserException::class);
        $this->expectExceptionMessage(
            'Invalid internal character after at position ' . $expectedInvalidInternalCharacterPosition
        );
        $this->expectExceptionCode(1);

        $this->parser->parse($attribute);
    }

    public function parseInvalidInternalCharacterDataProvider(): array
    {
        return [
            'ch arset=ISO-8859-4' => [
                'attribute' => 'ch ar set=ISO-8859-4',
                'expectedInvalidInternalCharacterPosition' => 2
            ],
        ];
    }

    /**
     * @dataProvider parseInvalidInternalCharacterDataProviderFoo
     */
    public function testParseInvalidInternalCharacterAttemptRecoveryIgnoreInvalidAttributes(string $attribute)
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->parser->getConfiguration()->enableIgnoreInvalidAttributes();

        $this->assertEmpty($this->parser->parse($attribute));
    }

    public function parseInvalidInternalCharacterDataProviderFoo(): array
    {
        return [
            'ch ar set=ISO-8859-4' => [
                'attribute' => 'ch ar set=ISO-8859-4',
            ],
        ];
    }

    /**
     * @dataProvider parseAndFixInvalidInternalCharacterDataProvider
     */
    public function testParseAndFixInvalidInternalCharacter(string $attribute, string $expectedName)
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    public function parseAndFixInvalidInternalCharacterDataProvider(): array
    {
        return [
            'charset: utf8' => [
                'attribute' => 'charset: utf8',
                'expectedName' => 'charset',
            ],
        ];
    }

    /**
     * @dataProvider parseAndIgnoreInvalidCharacterDataProvider
     */
    public function testParseAndIgnoreInvalidCharacter(string $attribute)
    {
        $this->parser->getConfiguration()->enableIgnoreInvalidAttributes();
        $this->assertEmpty($this->parser->parse($attribute));
    }

    public function parseAndIgnoreInvalidCharacterDataProvider(): array
    {
        return [
            'charset"foo": utf8' => [
                'attribute' => 'charset"foo": utf8',
            ],
        ];
    }
}
