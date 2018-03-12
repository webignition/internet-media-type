<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\SubtypeParser;
use webignition\InternetMediaType\Parser\SubtypeParserException;
use webignition\Tests\InternetMediaType\BaseTest;

class SubtypeParserTest extends BaseTest
{
    /**
     *
     * @var SubtypeParser
     */
    private $parser;

    public function setUp()
    {
        parent::setUp();
        $this->parser = new SubtypeParser();
    }

    /**
     * @dataProvider parseDataProvider
     *
     * @param string $internetMediaTypeString
     * @param string $expectedSubtype
     */
    public function testParse($internetMediaTypeString, $expectedSubtype)
    {
        $this->assertEquals(
            $expectedSubtype,
            $this->parser->parse($internetMediaTypeString)
        );
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        return [
            'without parameters' => [
                'internetMediaTypeString' => 'image/png',
                'expectedSubtype' => 'png',
            ],
            'with parameters' => [
                'internetMediaTypeString' => 'text/html; charset=utf-8',
                'expectedSubtype' => 'html',
            ],
        ];
    }

    public function testParseInvalidSubtype()
    {
        $this->expectException(SubtypeParserException::class);
        $this->expectExceptionMessage('Invalid internal character after at position 6');
        $this->expectExceptionCode(1);

        $this->parser->parse('text/h t m l; charset=ISO-8859-4');
    }

    /**
     * @dataProvider parseAndFixInvalidSubtypeDataProvider
     *
     * @param $internetMediaTypeString
     * @param $expectedSubtype
     */
    public function testParseAndFixInvalidSubtype($internetMediaTypeString, $expectedSubtype)
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();

        $this->assertEquals($expectedSubtype, $this->parser->parse($internetMediaTypeString));
    }

    /**
     * @return array
     */
    public function parseAndFixInvalidSubtypeDataProvider()
    {
        return [
            'type/subtype doubled and comma-separated' => [
                'internetMediaTypeString' => 'text/plain, text/plain',
                'expectedSubtype' => 'plain'
            ],
            'type/subtype with parameters separated by space instead of semicolon' => [
                'internetMediaTypeString' => 'text/html charset=utf-8',
                'expectedSubtype' => 'html'
            ],
        ];
    }
}
