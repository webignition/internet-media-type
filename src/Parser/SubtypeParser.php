<?php

namespace webignition\InternetMediaType\Parser;

use webignition\StringParser\StringParser;

/**
 * Parses out the subtype from an internet media type string
 *
 */
class SubtypeParser extends StringParser
{
    public const TYPE_SUBTYPE_SEPARATOR = '/';
    public const TYPE_PARAMETER_SEPARATOR = ';';
    public const STATE_IN_TYPE = 1;
    public const STATE_IN_SUBTYPE = 2;
    public const STATE_LEFT_SUBTYPE = 3;
    public const STATE_INVALID_INTERNAL_CHARACTER = 4;

    /**
     * Collection of characters not valid in a subtype
     *
     * @var string[]
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

    public function setConfiguration(Configuration $configuration): void
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
     * @throws SubtypeParserException
     */
    public function parse($inputString): string
    {
        return parent::parse(trim($inputString));
    }

    /**
     * @return string
     *
     * @throws SubtypeParserException
     */
    protected function parseCurrentCharacter(): ?string
    {
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                $this->setCurrentState(self::STATE_IN_TYPE);
                break;

            case self::STATE_IN_TYPE:
                if ($this->isCurrentCharacterTypeSubtypeSeparator()) {
                    $this->setCurrentState(self::STATE_IN_SUBTYPE);
                }

                $this->incrementCurrentCharacterPointer();

                break;

            case self::STATE_IN_SUBTYPE:
                if ($this->isCurrentCharacterInvalid()) {
                    $this->setCurrentState(self::STATE_INVALID_INTERNAL_CHARACTER);
                } elseif ($this->isCurrentCharacterTypeParameterSeparator()) {
                    $this->setCurrentState(self::STATE_LEFT_SUBTYPE);
                } else {
                    $this->appendOutputString();
                    $this->incrementCurrentCharacterPointer();
                }

                break;

            case self::STATE_LEFT_SUBTYPE:
                $this->stop();
                break;

            case self::STATE_INVALID_INTERNAL_CHARACTER:
                if ($this->shouldAttemptToFixInvalidInternalCharacter()) {
                    $this->hasAttemptedToFixAttributeInvalidInternalCharacter = true;

                    $fixer = new TypeFixer();
                    $fixer->setInputString($this->getInputString());
                    $fixer->setPosition($this->getCurrentCharacterPointer());
                    $fixedType = $fixer->fix();

                    return $this->parse((string) $fixedType);
                }

                throw new SubtypeParserException(
                    'Invalid internal character after at position ' . $this->getCurrentCharacterPointer(),
                    SubtypeParserException::INTERNAL_INVALID_CHARACTER_CODE,
                    $this->getCurrentCharacterPointer()
                );
        }

        return null;
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

    private function isCurrentCharacterTypeSubtypeSeparator(): bool
    {
        return $this->getCurrentCharacter() == self::TYPE_SUBTYPE_SEPARATOR;
    }

    private function isCurrentCharacterTypeParameterSeparator(): bool
    {
        return $this->getCurrentCharacter() == self::TYPE_PARAMETER_SEPARATOR;
    }
}
