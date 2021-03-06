<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parser\Configuration;
use webignition\StringParser\StringParser;

/**
 * Parses out the attribute name from an internet media type parameter string
 */
class AttributeParser extends StringParser
{
    const ATTRIBUTE_VALUE_SEPARATOR = '=';
    const STATE_IN_ATTRIBUTE_NAME = 1;
    const STATE_INVALID_INTERNAL_CHARACTER = 2;
    const STATE_LEFT_ATTRIBUTE_NAME = 3;

    /**
     * Collection of characters not valid in an attribute name
     *
     * @var array
     */
    private $invalidCharacters = array(
        ' ',
        '"',
        '\\'
    );

    /**
     * @var bool
     */
    private $hasAttemptedToFixAttributeInvalidInternalCharacter = false;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration();
    }

    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @param string $inputString
     *
     * @return string
     *
     * @throws AttributeParserException
     */
    public function parse($inputString): string
    {
        return parent::parse(trim($inputString));
    }

    /**
     * @return string
     *
     * @throws AttributeParserException
     */
    protected function parseCurrentCharacter(): ?string
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

                    return $this->parse($fixedInputString);
                }

                throw new AttributeParserException(
                    'Invalid internal character after at position '.$this->getCurrentCharacterPointer(),
                    1,
                    $this->getCurrentCharacterPointer()
                );
        }

        return null;
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
        return $this->getCurrentCharacter() == self::ATTRIBUTE_VALUE_SEPARATOR;
    }
}
