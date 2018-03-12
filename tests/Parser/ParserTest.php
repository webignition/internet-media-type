<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\Configuration;
use webignition\InternetMediaType\Parser\Parser;
use webignition\Tests\InternetMediaType\BaseTest;

class ParserTest extends BaseTest
{
    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();
        $this->parser = new Parser();
    }

    /**
     * @dataProvider parseValidMediaTypeDataProvider
     *
     * @param string $internetMediaTypeString
     * @param string $expectedType
     * @param string $expectedSubtype
     * @param array $expectedParameters
     */
    public function testParseValidMediaType(
        $internetMediaTypeString,
        $expectedType,
        $expectedSubtype,
        $expectedParameters
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

    /**
     * @return array
     */
    public function parseValidMediaTypeDataProvider()
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
     *
     * @param string $internetMediaTypeString
     * @param string $expectedParsedMediaTypeString
     * @param string $expectedType
     * @param string $expectedSubtype
     * @param array $expectedParameters
     */
    public function testParseAndFixInvalidMediaType(
        $internetMediaTypeString,
        $expectedParsedMediaTypeString,
        $expectedType,
        $expectedSubtype,
        $expectedParameters
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

    /**
     * @return array
     */
    public function parseAndFixInvalidMediaTypeDataProvider()
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
     *
     * @param bool$ignoreInvalidAttributes
     */
    public function testSetIgnoreInvalidAttributes($ignoreInvalidAttributes)
    {
        $this->parser->setIgnoreInvalidAttributes($ignoreInvalidAttributes);
        $this->assertEquals($ignoreInvalidAttributes, $this->parser->getConfiguration()->ignoreInvalidAttributes());
    }

    /**
     * @return array
     */
    public function setIgnoreInvalidAttributesDataProvider()
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
     *
     * @param bool$attemptToRecoverFromInvalidInternalCharacter
     */
    public function testSetAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter)
    {
        $this->parser->setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter);
        $this->assertEquals(
            $attemptToRecoverFromInvalidInternalCharacter,
            $this->parser->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
        );
    }

    /**
     * @return array
     */
    public function setAttemptToRecoverFromInvalidInternalCharacterDataProvider()
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
}
