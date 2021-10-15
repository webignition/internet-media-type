<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\Exception\TypeParserException;
use webignition\StringParser\StringParser;
use webignition\StringParser\UnknownStateException;

/**
 * Parses out the type from an internet media type string.
 */
class TypeParser
{
    public const TYPE_SUBTYPE_SEPARATOR = '/';
    public const STATE_IN_TYPE = 1;
    public const STATE_INVALID_INTERNAL_CHARACTER = 2;
    public const STATE_LEFT_TYPE = 3;

    /**
     * Collection of characters not valid in a type.
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
            self::STATE_LEFT_TYPE => function (StringParser $stringParser) {
                $stringParser->stop();
            },
            self::STATE_INVALID_INTERNAL_CHARACTER => function (StringParser $stringParser) {
                throw new TypeParserException(
                    'Invalid internal character after at position ' . $stringParser->getPointer(),
                    1
                );
            },
        ]);
    }

    /**
     * @throws TypeParserException
     * @throws UnknownStateException
     */
    public function parse(string $input): string
    {
        return $this->stringParser->parse(trim($input));
    }

    private function handleInTypeState(StringParser $stringParser): void
    {
        $character = $stringParser->getCurrentCharacter();
        $isCharacterInvalid = in_array($character, $this->invalidCharacters);
        $isCharacterTypeSubtypeSeparator = self::TYPE_SUBTYPE_SEPARATOR === $character;

        if ($isCharacterInvalid || $isCharacterTypeSubtypeSeparator) {
            $stringParser->setState(
                $isCharacterInvalid ? self::STATE_INVALID_INTERNAL_CHARACTER : self::STATE_LEFT_TYPE
            );
        } else {
            $stringParser->appendOutputString();
            $stringParser->incrementPointer();
        }
    }
}
