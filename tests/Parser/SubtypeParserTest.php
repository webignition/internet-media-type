<?php
/** @noinspection PhpDocSignatureInspection */
/** @noinspection PhpUnhandledExceptionInspection */

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\SubtypeParser;
use webignition\InternetMediaType\Parser\SubtypeParserException;

class SubtypeParserTest extends \PHPUnit\Framework\TestCase
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
     */
    public function testParse(string $internetMediaTypeString, string $expectedSubtype)
    {
        $this->assertEquals(
            $expectedSubtype,
            $this->parser->parse($internetMediaTypeString)
        );
    }

    public function parseDataProvider(): array
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

    /**
     * @throws SubtypeParserException
     */
    public function testParseInvalidSubtype()
    {
        $this->expectException(SubtypeParserException::class);
        $this->expectExceptionMessage('Invalid internal character after at position 6');
        $this->expectExceptionCode(1);

        $this->parser->parse('text/h t m l; charset=ISO-8859-4');
    }

    /**
     * @dataProvider parseAndFixInvalidSubtypeDataProvider
     */
    public function testParseAndFixInvalidSubtype(string $internetMediaTypeString, string $expectedSubtype)
    {
        $this->parser->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();

        $this->assertEquals($expectedSubtype, $this->parser->parse($internetMediaTypeString));
    }

    public function parseAndFixInvalidSubtypeDataProvider(): array
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
