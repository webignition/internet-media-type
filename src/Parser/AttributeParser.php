<?php

namespace webignition\InternetMediaType\Parser;

use webignition\StringParser\StringParser;
use webignition\StringParser\UnknownStateException;

/**
 * Parses out the attribute name from an internet media type parameter string.
 */
class AttributeParser
{
    public const ATTRIBUTE_VALUE_SEPARATOR = '=';
    public const STATE_IN_ATTRIBUTE_NAME = 1;
    public const STATE_INVALID_INTERNAL_CHARACTER = 2;
    public const STATE_LEFT_ATTRIBUTE_NAME = 3;

    /**
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
                $this->handleUnknownState($stringParser);
            },
            self::STATE_IN_ATTRIBUTE_NAME => function (StringParser $stringParser) {
                $this->handleInAttributeNameState($stringParser);
            },
            self::STATE_LEFT_ATTRIBUTE_NAME => function (StringParser $stringParser) {
                $stringParser->stop();
            },
            self::STATE_INVALID_INTERNAL_CHARACTER => function (StringParser $stringParser) {
                $this->handleInvalidInternalCharacterState($stringParser);
            },
        ]);
    }

    /**
     * @throws AttributeParserException
     * @throws UnknownStateException
     */
    public function parse(string $inputString): string
    {
        return $this->stringParser->parse(trim($inputString));
    }

    private function handleUnknownState(StringParser $stringParser): void
    {
        $stringParser->setState(self::STATE_IN_ATTRIBUTE_NAME);
    }

    private function handleInAttributeNameState(StringParser $stringParser): void
    {
        $character = $stringParser->getCurrentCharacter();

        $isCharacterInvalid = in_array($character, $this->invalidCharacters);
        $isCharacterAttributeValueSeparator = self::ATTRIBUTE_VALUE_SEPARATOR === $character;

        if ($isCharacterInvalid) {
            $stringParser->setState(self::STATE_INVALID_INTERNAL_CHARACTER);
        } elseif ($isCharacterAttributeValueSeparator) {
            $stringParser->setState(self::STATE_LEFT_ATTRIBUTE_NAME);
        } else {
            $stringParser->appendOutputString();
            $stringParser->incrementPointer();
        }
    }

    /**
     * @throws AttributeParserException
     */
    private function handleInvalidInternalCharacterState(StringParser $stringParser): void
    {
        throw new AttributeParserException(
            'Invalid internal character after at position ' . $stringParser->getPointer(),
            1,
            $stringParser->getPointer()
        );
    }
}
