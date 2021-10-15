<?php

namespace webignition\Tests\InternetMediaType\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parser\Configuration;
use webignition\InternetMediaType\Parser\ParseException;
use webignition\InternetMediaType\Parser\Parser;
use webignition\InternetMediaType\Parser\SubtypeParserException;
use webignition\InternetMediaType\Parser\TypeParserException;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;

class ParserTest extends TestCase
{
    protected Parser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = Parser::create();
    }

    /**
     * @dataProvider parseValidMediaTypeDataProvider
     *
     * @param array<string, string> $expectedParameters
     */
    public function testParseValidMediaType(
        string $internetMediaTypeString,
        string $expectedType,
        string $expectedSubtype,
        array $expectedParameters
    ): void {
        $internetMediaType = $this->parser->parse($internetMediaTypeString);
        self::assertInstanceOf(InternetMediaTypeInterface::class, $internetMediaType);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());

        $this->assertCount(count($expectedParameters), $internetMediaType->getParameters());

        foreach ($internetMediaType->getParameters() as $attribute => $parameter) {
            $this->assertTrue(isset($expectedParameters[$attribute]));
            $this->assertEquals($expectedParameters[$attribute], $parameter->getValue());
        }
    }

    /**
     * @return array<mixed>
     */
    public function parseValidMediaTypeDataProvider(): array
    {
        return [
            'image/png' => [
                'internetMediaTypeString' => 'image/png',
                'expectedType' => 'image',
                'expectedSubtype' => 'png',
                'expectedParameters' => [],
            ],
            'text/html' => [
                'internetMediaTypeString' => 'text/html',
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedParameters' => [],
            ],
            'text/html; charset=utf-8' => [
                'internetMediaTypeString' => 'text/html; charset=utf-8',
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedParameters' => [
                    'charset' => 'utf-8',
                ],
            ],
            'text/html; charset="utf-8"' => [
                'internetMediaTypeString' => 'text/html; charset="utf-8"',
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedParameters' => [
                    'charset' => 'utf-8',
                ],
            ],
            'foo/bar; attr1=value; attr2=value2; attr3=value3' => [
                'internetMediaTypeString' => 'foo/bar; attr1=value1; attr2=value2; attr3=value3',
                'expectedType' => 'foo',
                'expectedSubtype' => 'bar',
                'expectedParameters' => [
                    'attr1' => 'value1',
                    'attr2' => 'value2',
                    'attr3' => 'value3',
                ],
            ],
        ];
    }

    /**
     * @dataProvider ignoreInvalidAttributesDataProvider
     */
    public function testIgnoreInvalidAttributes(string $internetMediaTypeString, string $expected): void
    {
        $this->parser->setIgnoreInvalidAttributes(true);
        $internetMediaType = $this->parser->parse($internetMediaTypeString);

        $this->assertEquals($expected, (string) $internetMediaType);
    }

    /**
     * @return array<mixed>
     */
    public function ignoreInvalidAttributesDataProvider(): array
    {
        return [
            'single invalid attribute only' => [
                'internetMediaTypeString' => 'foo/bar; charset: UTF-8',
                'expected' => 'foo/bar',
            ],
            'single trailing invalid attribute' => [
                'internetMediaTypeString' => 'foo/bar; attribute=value; charset: UTF-8',
                'expected' => 'foo/bar; attribute=value',
            ],
            'single leading invalid attribute' => [
                'internetMediaTypeString' => 'foo/bar; charset: UTF-8; attribute=value',
                'expected' => 'foo/bar; attribute=value',
            ],
        ];
    }

    /**
     * @dataProvider parseAndFixInvalidMediaTypeDataProvider
     *
     * @param array<string, string> $expectedParameters
     */
    public function testParseAndFixInvalidMediaType(
        string $internetMediaTypeString,
        string $expectedParsedMediaTypeString,
        string $expectedType,
        string $expectedSubtype,
        array $expectedParameters
    ): void {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $internetMediaType = $this->parser->parse($internetMediaTypeString);
        self::assertInstanceOf(InternetMediaTypeInterface::class, $internetMediaType);

        $this->assertEquals($expectedParsedMediaTypeString, (string) $internetMediaType);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());

        $this->assertCount(count($expectedParameters), $internetMediaType->getParameters());

        foreach ($internetMediaType->getParameters() as $attribute => $parameter) {
            $this->assertTrue(isset($expectedParameters[$attribute]));
            $this->assertEquals($expectedParameters[$attribute], $parameter->getValue());
        }
    }

    /**
     * @return array<mixed>
     */
    public function parseAndFixInvalidMediaTypeDataProvider(): array
    {
        return [
            'application/x-javascript, application/x-javascript; charset=utf-8' => [
                'internetMediaTypeString' => 'application/x-javascript, application/x-javascript; charset=utf-8',
                'expectedParsedMediaTypeString' => 'application/x-javascript; charset=utf-8',
                'expectedType' => 'application',
                'expectedSubtype' => 'x-javascript',
                'expectedParameters' => [
                    'charset' => 'utf-8',
                ],
            ],
            'text/html charset=UTF-8' => [
                'internetMediaTypeString' => 'text/html charset=UTF-8',
                'expectedParsedMediaTypeString' => 'text/html; charset=UTF-8',
                'expectedType' => 'text',
                'expectedSubtype' => 'html',
                'expectedParameters' => [
                    'charset' => 'UTF-8',
                ],
            ],
            'text/css; charset: UTF-8' => [
                'internetMediaTypeString' => 'text/css; charset: UTF-8',
                'expectedParsedMediaTypeString' => 'text/css; charset=UTF-8',
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedParameters' => [
                    'charset' => 'UTF-8',
                ],
            ],
            'text/css charset: UTF-8' => [
                'internetMediaTypeString' => 'text/css charset: UTF-8',
                'expectedParsedMediaTypeString' => 'text/css; charset=UTF-8',
                'expectedType' => 'text',
                'expectedSubtype' => 'css',
                'expectedParameters' => [
                    'charset' => 'UTF-8',
                ],
            ],
        ];
    }

    public function testSetConfiguration(): void
    {
        $configuration = new Configuration();

        $this->assertNotEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));

        $this->parser->setConfiguration($configuration);
        $this->assertEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));
    }

    /**
     * @dataProvider setIgnoreInvalidAttributesDataProvider
     */
    public function testSetIgnoreInvalidAttributes(bool $ignoreInvalidAttributes): void
    {
        $this->parser->setIgnoreInvalidAttributes($ignoreInvalidAttributes);
        $this->assertEquals($ignoreInvalidAttributes, $this->parser->getConfiguration()->ignoreInvalidAttributes());
    }

    /**
     * @return array<mixed>
     */
    public function setIgnoreInvalidAttributesDataProvider(): array
    {
        return [
            'true' => [
                'ignoreInvalidAttributes' => true,
            ],
            'false' => [
                'ignoreInvalidAttributes' => false,
            ],
        ];
    }

    /**
     * @dataProvider setAttemptToRecoverFromInvalidInternalCharacterDataProvider
     */
    public function testSetAttemptToRecoverFromInvalidInternalCharacter(
        bool $attemptToRecoverFromInvalidInternalCharacter
    ): void {
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter);
        $this->assertEquals(
            $attemptToRecoverFromInvalidInternalCharacter,
            $this->parser->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
        );
    }

    /**
     * @return array<mixed>
     */
    public function setAttemptToRecoverFromInvalidInternalCharacterDataProvider(): array
    {
        return [
            'true' => [
                'attemptToRecoverFromInvalidInternalCharacter' => true,
            ],
            'false' => [
                'attemptToRecoverFromInvalidInternalCharacter' => false,
            ],
        ];
    }

    /**
     * @dataProvider parseThrowsExceptionDataProvider
     *
     * @param class-string $expectedPreviousExceptionClass
     */
    public function testParseThrowsException(
        string $contentTypeString,
        string $expectedMessage,
        int $expectedCode,
        string $expectedPreviousExceptionClass
    ): void {
        try {
            $this->parser->parse($contentTypeString);
            $this->fail(ParseException::class . ' not thrown');
        } catch (ParseException $parseException) {
            $this->assertEquals($expectedMessage, $parseException->getMessage());
            $this->assertEquals($expectedCode, $parseException->getCode());
            $this->assertInstanceOf($expectedPreviousExceptionClass, $parseException->getPrevious());
            $this->assertEquals($contentTypeString, $parseException->getContentTypeString());
        }
    }

    /**
     * @return array<mixed>
     */
    public function parseThrowsExceptionDataProvider(): array
    {
        return [
            'type parser exception' => [
                'contentTypeString' => 'f o o',
                'expectedMessage' => 'Invalid internal character after at position 1',
                'expectedCode' => 1,
                'expectedPreviousExceptionClass' => TypeParserException::class,
            ],
            'subtype parser exception' => [
                'contentTypeString' => 'text/h t m l',
                'expectedMessage' => 'Invalid internal character after at position 6',
                'expectedCode' => 1,
                'expectedPreviousExceptionClass' => SubtypeParserException::class,
            ],
            'attribute parser exception' => [
                'contentTypeString' => 'text/html; c h a r s e t',
                'expectedMessage' => 'Invalid internal character after at position 1',
                'expectedCode' => 1,
                'expectedPreviousExceptionClass' => AttributeParserException::class,
            ],
        ];
    }
}
