<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\AttributeParser;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;

class AttributeParserTest extends TestCase
{
    private AttributeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new AttributeParser();
    }

    /**
     * @dataProvider parseDataProvider
     */
    public function testParse(string $attribute, string $expectedName): void
    {
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    /**
     * @return array<mixed>
     */
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
    public function testParseInvalidInternalCharacter(
        string $attribute,
        int $expectedInvalidInternalCharacterPosition
    ): void {
        $this->expectException(AttributeParserException::class);
        $this->expectExceptionMessage(
            'Invalid internal character after at position ' . $expectedInvalidInternalCharacterPosition
        );
        $this->expectExceptionCode(1);

        $this->parser->parse($attribute);
    }

    /**
     * @return array<mixed>
     */
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
    public function testParseInvalidInternalCharacterAttemptRecoveryIgnoreInvalidAttributes(string $attribute): void
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->parser->getConfiguration()->enableIgnoreInvalidAttributes();

        $this->assertEmpty($this->parser->parse($attribute));
    }

    /**
     * @return array<mixed>
     */
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
    public function testParseAndFixInvalidInternalCharacter(string $attribute, string $expectedName): void
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertEquals($expectedName, $this->parser->parse($attribute));
    }

    /**
     * @return array<mixed>
     */
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
    public function testParseAndIgnoreInvalidCharacter(string $attribute): void
    {
        $this->parser->getConfiguration()->enableIgnoreInvalidAttributes();
        $this->assertEmpty($this->parser->parse($attribute));
    }

    /**
     * @return array<mixed>
     */
    public function parseAndIgnoreInvalidCharacterDataProvider(): array
    {
        return [
            'charset"foo": utf8' => [
                'attribute' => 'charset"foo": utf8',
            ],
        ];
    }
}
