<?php

namespace webignition\InternetMediaType\Parser;

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

    private bool $hasAttemptedToFixAttributeInvalidInternalCharacter = false;

    private Configuration $configuration;
    private StringParser $stringParser;

    public function __construct()
    {
        $this->configuration = new Configuration();
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
                $this->handleInvalidInternalCharacterState($stringParser);
            },
        ]);
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
     * @throws SubtypeParserException
     * @throws UnknownStateException
     */
    public function parse(string $input): string
    {
        return $this->stringParser->parse(trim($input));
    }

    private function shouldAttemptToFixInvalidInternalCharacter(): bool
    {
        return $this->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
            && !$this->hasAttemptedToFixAttributeInvalidInternalCharacter;
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

    /**
     * @throws SubtypeParserException
     * @throws UnknownStateException
     */
    private function handleInvalidInternalCharacterState(StringParser $stringParser): void
    {
        $pointer = $stringParser->getPointer();

        if ($this->shouldAttemptToFixInvalidInternalCharacter()) {
            $this->hasAttemptedToFixAttributeInvalidInternalCharacter = true;

            $fixer = new TypeFixer();
            $fixer->setInputString($stringParser->getInput());
            $fixer->setPosition($pointer);
            $fixedType = $fixer->fix();

            $this->parse((string) $fixedType);

            return;
        }

        throw new SubtypeParserException(
            sprintf('Invalid internal character after at position %d', $pointer),
            SubtypeParserException::INTERNAL_INVALID_CHARACTER_CODE,
            $pointer
        );
    }
}
