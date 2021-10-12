<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\QuotedString\Parser as QuotedStringParser;
use webignition\StringParser\StringParser;

/**
 * Parses out the value from an internet media type parameter string.
 */
class ValueParser extends StringParser
{
    public const ATTRIBUTE_VALUE_SEPARATOR = '=';
    public const QUOTED_STRING_DELIMITER = '"';
    public const STATE_IN_NON_QUOTED_VALUE = 1;
    public const STATE_IN_QUOTED_VALUE = 2;

    /**
     * Attribute part of the attribute=value parameter string.
     */
    private string $attribute = '';

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function parse(string $inputString): string
    {
        $output = parent::parse($this->getNonAttributePart($inputString));

        if (self::STATE_IN_NON_QUOTED_VALUE == $this->getCurrentState()) {
            return $output;
        }

        if ('' == $output) {
            return '';
        }

        $quotedStringParser = new QuotedStringParser();
        $quotedString = $quotedStringParser->parseToObject($output);

        return $quotedString->getValue();
    }

    protected function parseCurrentCharacter(): void
    {
        switch ($this->getCurrentState()) {
            case self::STATE_UNKNOWN:
                $this->deriveState();

                break;

            default:
                $this->appendOutputString();
                $this->incrementCurrentCharacterPointer();

                break;
        }
    }

    private function getNonAttributePart(string $inputString): string
    {
        return trim(substr(
            $inputString,
            strlen($this->attribute) + strlen(self::ATTRIBUTE_VALUE_SEPARATOR)
        ));
    }

    private function deriveState(): void
    {
        if ($this->isCurrentCharacterQuotedStringDelimiter()) {
            $this->setCurrentState(self::STATE_IN_QUOTED_VALUE);
        } else {
            $this->setCurrentState(self::STATE_IN_NON_QUOTED_VALUE);
        }
    }

    private function isCurrentCharacterQuotedStringDelimiter(): bool
    {
        return self::QUOTED_STRING_DELIMITER == $this->getCurrentCharacter();
    }
}
