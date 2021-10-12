<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\StringParser\StringParser;
use webignition\QuotedString\Parser as QuotedStringParser;

/**
 * Parses out the value from an internet media type parameter string
 */
class ValueParser extends StringParser
{
    public const ATTRIBUTE_VALUE_SEPARATOR = '=';
    public const QUOTED_STRING_DELIMITER = '"';
    public const STATE_IN_NON_QUOTED_VALUE = 1;
    public const STATE_IN_QUOTED_VALUE = 2;

    /**
     * Attribute part of the attribute=value parameter string
     *
     * @var string
     */
    private $attribute = '';

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @param string $inputString
     *
     * @return string
     */
    public function parse($inputString): string
    {
        $output = parent::parse($this->getNonAttributePart($inputString));

        if ($this->getCurrentState() == self::STATE_IN_NON_QUOTED_VALUE) {
            return $output;
        }

        if ($output == '') {
            return '';
        }

        $quotedStringParser = new QuotedStringParser();
        $quotedString = $quotedStringParser->parseToObject($output);

        return $quotedString->getValue();
    }

    private function getNonAttributePart(string $inputString): string
    {
        return trim(substr(
            $inputString,
            strlen($this->attribute) + strlen(self::ATTRIBUTE_VALUE_SEPARATOR)
        ));
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
        return $this->getCurrentCharacter() == self::QUOTED_STRING_DELIMITER;
    }
}
