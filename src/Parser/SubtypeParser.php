<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\Exception\SubtypeParserException;
use webignition\StringParser\StringParser;
use webignition\StringParser\UnknownStateException;

/**
 * Parses out the subtype from an internet media type string.
 */
class SubtypeParser
{
    public const TYPE_SUBTYPE_SEPARATOR = '/';
    public const TYPE_PARAMETER_SEPARATOR = ';';
    public const STATE_IN_TYPE = 1;
    public const STATE_IN_SUBTYPE = 2;
    public const STATE_LEFT_SUBTYPE = 3;
    public const STATE_INVALID_INTERNAL_CHARACTER = 4;

    /**
     * Collection of characters not valid in a subtype.
     *
     * @var string[]
     */
    private array $invalidCharacters = [
        ' ',
        '"',
        '\\'
    ];

    private StringParser $stringParser;

    public function __construct()
    {
        $this->stringParser = new StringParser([
            StringParser::STATE_UNKNOWN => function (StringParser $stringParser) {
                $stringParser->setState(self::STATE_IN_TYPE);
            },
            self::STATE_IN_TYPE => function (StringParser $stringParser) {
                $this->handleInTypeState($stringParser);
            },
            self::STATE_IN_SUBTYPE => function (StringParser $stringParser) {
                $this->handleInSubtypeState($stringParser);
            },
            self::STATE_LEFT_SUBTYPE => function (StringParser $stringParser) {
                $stringParser->stop();
            },
            self::STATE_INVALID_INTERNAL_CHARACTER => function (StringParser $stringParser) {
                $pointer = $stringParser->getPointer();

                throw new SubtypeParserException(
                    sprintf('Invalid internal character after at position %d', $pointer),
                    SubtypeParserException::INTERNAL_INVALID_CHARACTER_CODE,
                    $pointer
                );
            },
        ]);
    }

    /**
     * @throws SubtypeParserException
     * @throws UnknownStateException
     */
    public function parse(string $input): string
    {
        return $this->stringParser->parse(trim($input));
    }

    private function handleInTypeState(StringParser $stringParser): void
    {
        if (self::TYPE_SUBTYPE_SEPARATOR === $stringParser->getCurrentCharacter()) {
            $stringParser->setState(self::STATE_IN_SUBTYPE);
        }

        $stringParser->incrementPointer();
    }

    private function handleInSubtypeState(StringParser $stringParser): void
    {
        $isCharacterInvalid = in_array($stringParser->getCurrentCharacter(), $this->invalidCharacters);

        if ($isCharacterInvalid) {
            $stringParser->setState(self::STATE_INVALID_INTERNAL_CHARACTER);
        } elseif (self::TYPE_PARAMETER_SEPARATOR === $stringParser->getCurrentCharacter()) {
            $stringParser->setState(self::STATE_LEFT_SUBTYPE);
        } else {
            $stringParser->appendOutputString();
            $stringParser->incrementPointer();
        }
    }
}
