<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parser\Configuration;
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

    private bool $hasAttemptedToFixAttributeInvalidInternalCharacter = false;

    private StringParser $stringParser;
    private Configuration $configuration;
    private AttributeFixer $attributeFixer;

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

        $this->configuration = new Configuration();
        $this->attributeFixer = new AttributeFixer();
    }

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
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
            if ($this->shouldIgnoreInvalidCharacter()) {
                $stringParser->incrementPointer();
                $stringParser->setState(self::STATE_LEFT_ATTRIBUTE_NAME);
                $stringParser->clearOutput();
            } else {
                $stringParser->setState(self::STATE_INVALID_INTERNAL_CHARACTER);
            }
        } elseif ($isCharacterAttributeValueSeparator) {
            $stringParser->setState(self::STATE_LEFT_ATTRIBUTE_NAME);
        } else {
            $stringParser->appendOutputString();
            $stringParser->incrementPointer();
        }
    }

    /**
     * @throws AttributeParserException
     * @throws UnknownStateException
     */
    private function handleInvalidInternalCharacterState(StringParser $stringParser): void
    {
        if ($this->shouldAttemptToFixInvalidInternalCharacter()) {
            $this->hasAttemptedToFixAttributeInvalidInternalCharacter = true;

            $fixedInputString = $this->attributeFixer->fix($stringParser->getInput());

            $this->parse($fixedInputString);

            return;
        }

        throw new AttributeParserException(
            'Invalid internal character after at position ' . $stringParser->getPointer(),
            1,
            $stringParser->getPointer()
        );
    }

    private function shouldIgnoreInvalidCharacter(): bool
    {
        if (false === $this->getConfiguration()->ignoreInvalidAttributes()) {
            return false;
        }

        if (false === $this->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()) {
            return true;
        }

        if ($this->hasAttemptedToFixAttributeInvalidInternalCharacter) {
            return true;
        }

        return false;
    }

    private function shouldAttemptToFixInvalidInternalCharacter(): bool
    {
        return $this->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
            && !$this->hasAttemptedToFixAttributeInvalidInternalCharacter;
    }
}
