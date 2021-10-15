<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\QuotedString\Exception as QuotedStringException;
use webignition\QuotedString\Parser as QuotedStringParser;
use webignition\StringParser\StringParser;
use webignition\StringParser\UnknownStateException;

class ValueParser
{
    public const ATTRIBUTE_VALUE_SEPARATOR = '=';
    public const QUOTED_STRING_DELIMITER = '"';
    public const STATE_IN_NON_QUOTED_VALUE = 1;
    public const STATE_IN_QUOTED_VALUE = 2;

    /**
     * Attribute part of the attribute=value parameter string.
     */
    private string $attribute = '';

    private StringParser $stringParser;

    public function __construct()
    {
        $this->stringParser = new StringParser([
            StringParser::STATE_UNKNOWN => function (StringParser $stringParser) {
                if (self::QUOTED_STRING_DELIMITER === $stringParser->getCurrentCharacter()) {
                    $stringParser->setState(self::STATE_IN_QUOTED_VALUE);
                } else {
                    $stringParser->setState(self::STATE_IN_NON_QUOTED_VALUE);
                }
            },
            self::STATE_IN_NON_QUOTED_VALUE => function (StringParser $stringParser) {
                $stringParser->appendOutputString();
                $stringParser->incrementPointer();
            },
            self::STATE_IN_QUOTED_VALUE => function (StringParser $stringParser) {
                $stringParser->appendOutputString();
                $stringParser->incrementPointer();
            },
        ]);
    }

    public function setAttribute(string $attribute): void
    {
        $this->attribute = $attribute;
    }

    /**
     * @throws QuotedStringException
     * @throws UnknownStateException
     */
    public function parse(string $input): string
    {
        $output = $this->stringParser->parse($this->getNonAttributePart($input));

        if (self::STATE_IN_NON_QUOTED_VALUE == $this->stringParser->getState()) {
            return $output;
        }

        if ('' == $output) {
            return '';
        }

        $quotedStringParser = new QuotedStringParser();
        $quotedString = $quotedStringParser->parseToObject($output);

        return $quotedString->getValue();
    }

    private function getNonAttributePart(string $input): string
    {
        return trim(substr(
            $input,
            strlen($this->attribute) + strlen(self::ATTRIBUTE_VALUE_SEPARATOR)
        ));
    }
}
