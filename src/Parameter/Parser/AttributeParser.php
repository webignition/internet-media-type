<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parser\Configuration;
use webignition\StringParser\StringParser;

/**
 * Parses out the attribute name from an internet media type parameter string.
 */
class AttributeParser extends StringParser
{
    public const ATTRIBUTE_VALUE_SEPARATOR = '=';
    public const STATE_IN_ATTRIBUTE_NAME = 1;
    public const STATE_INVALID_INTERNAL_CHARACTER = 2;
    public const STATE_LEFT_ATTRIBUTE_NAME = 3;

    /**
     * Collection of characters not valid in an attribute name.
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

    public function __construct()
    {
        $this->configuration = new Configuration();
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
     */
    public function parse(string $inputString): string
    {
        parent::setCurrentState(self::STATE_UNKNOWN);

        return parent::parse(trim($inputString));
    }

    /**
     * @throws AttributeParserException
     */
    protected function parseCurrentCharacter(): void
    {
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                $this->setCurrentState(self::STATE_IN_ATTRIBUTE_NAME);

                break;

            case self::STATE_IN_ATTRIBUTE_NAME:
                if ($this->isCurrentCharacterInvalid()) {
                    if ($this->shouldIgnoreInvalidCharacter()) {
                        $this->incrementCurrentCharacterPointer();
                        $this->setCurrentState(self::STATE_LEFT_ATTRIBUTE_NAME);
                        $this->clearOutputString();
                    } else {
                        $this->setCurrentState(self::STATE_INVALID_INTERNAL_CHARACTER);
                    }
                } elseif ($this->isCurrentCharacterAttributeValueSeparator()) {
                    $this->setCurrentState(self::STATE_LEFT_ATTRIBUTE_NAME);
                } else {
                    $this->appendOutputString();
                    $this->incrementCurrentCharacterPointer();
                }

                break;

            case self::STATE_LEFT_ATTRIBUTE_NAME:
                $this->stop();

                break;

            case self::STATE_INVALID_INTERNAL_CHARACTER:
                if ($this->shouldAttemptToFixInvalidInternalCharacter()) {
                    $this->hasAttemptedToFixAttributeInvalidInternalCharacter = true;

                    $attributeFixer = new AttributeFixer();
                    $attributeFixer->setInputString($this->getInputString());
                    $fixedInputString = $attributeFixer->fix();

                    $this->parse($fixedInputString);

                    return;
                }

                throw new AttributeParserException(
                    'Invalid internal character after at position ' . $this->getCurrentCharacterPointer(),
                    1,
                    $this->getCurrentCharacterPointer()
                );
        }
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

    private function isCurrentCharacterInvalid(): bool
    {
        return in_array($this->getCurrentCharacter(), $this->invalidCharacters);
    }

    private function isCurrentCharacterAttributeValueSeparator(): bool
    {
        return self::ATTRIBUTE_VALUE_SEPARATOR == $this->getCurrentCharacter();
    }
}
