<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parser\Configuration;
use webignition\InternetMediaType\Parser\ParseException;
use webignition\InternetMediaType\Parser\Parser;
use webignition\InternetMediaType\Parser\SubtypeParserException;
use webignition\InternetMediaType\Parser\TypeParserException;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseValidMediaTypeDataProvider
     */
    public function testParseValidMediaType(
        string  $internetMediaTypeString,
        string $expectedType,
        string $expectedSubtype,
        array $expectedParameters
    ) {
        $internetMediaType = $this->parser->parse($internetMediaTypeString);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());

        $this->assertCount(count($expectedParameters), $internetMediaType->getParameters());

        foreach ($internetMediaType->getParameters() as $attribute => $parameter) {
            $this->assertTrue(isset($expectedParameters[$attribute]));
            $this->assertEquals($expectedParameters[$attribute], $parameter->getValue());
        }
    }

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

    public function testIgnoreInvalidAttributes()
    {
        $this->parser->setIgnoreInvalidAttributes(true);
        $internetMediaType = $this->parser->parse('foo/bar; charset: UTF-8');

        $this->assertEquals('foo/bar', (string)$internetMediaType);
    }

    /**
     * @dataProvider parseAndFixInvalidMediaTypeDataProvider
     */
    public function testParseAndFixInvalidMediaType(
        string $internetMediaTypeString,
        string $expectedParsedMediaTypeString,
        string $expectedType,
        string $expectedSubtype,
        array $expectedParameters
    ) {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        $internetMediaType = $this->parser->parse($internetMediaTypeString);
        $this->assertEquals($expectedParsedMediaTypeString, (string)$internetMediaType);

        $this->assertEquals($expectedType, $internetMediaType->getType());
        $this->assertEquals($expectedSubtype, $internetMediaType->getSubtype());

        $this->assertCount(count($expectedParameters), $internetMediaType->getParameters());

        foreach ($internetMediaType->getParameters() as $attribute => $parameter) {
            $this->assertTrue(isset($expectedParameters[$attribute]));
            $this->assertEquals($expectedParameters[$attribute], $parameter->getValue());
        }
    }

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

    public function testSetConfiguration()
    {
        $configuration = new Configuration();

        $this->assertNotEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));

        $this->parser->setConfiguration($configuration);
        $this->assertEquals(spl_object_hash($configuration), spl_object_hash($this->parser->getConfiguration()));
    }

    /**
     * @dataProvider setIgnoreInvalidAttributesDataProvider
     */
    public function testSetIgnoreInvalidAttributes(bool $ignoreInvalidAttributes)
    {
        $this->parser->setIgnoreInvalidAttributes($ignoreInvalidAttributes);
        $this->assertEquals($ignoreInvalidAttributes, $this->parser->getConfiguration()->ignoreInvalidAttributes());
    }

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
    ) {
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter);
        $this->assertEquals(
            $attemptToRecoverFromInvalidInternalCharacter,
            $this->parser->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
        );
    }

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
     */
    public function testParseThrowsException(
        string $contentTypeString,
        string $expectedMessage,
        string $expectedCode,
        string $expectedPreviousExceptionClass
    ) {
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
