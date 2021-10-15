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

    private StringParser $stringParser;

    public function __construct(
        private QuotedStringParser $quotedStringParser,
    ) {
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

    public static function create(): ValueParser
    {
        return new ValueParser(
            new QuotedStringParser(),
        );
    }

    /**
     * @throws QuotedStringException
     * @throws UnknownStateException
     */
    public function parse(string $parameter, string $attribute): string
    {
        $nonAttributePart = $this->getNonAttributePart($parameter, $attribute);

        $output = $this->stringParser->parse($nonAttributePart);

        if (self::STATE_IN_NON_QUOTED_VALUE == $this->stringParser->getState()) {
            return $output;
        }

        if ('' == $output) {
            return '';
        }

        $quotedString = $this->quotedStringParser->parseToObject($output);

        return $quotedString->getValue();
    }

    private function getNonAttributePart(string $parameter, string $attribute): string
    {
        return trim(substr(
            $parameter,
            strlen($attribute) + strlen(self::ATTRIBUTE_VALUE_SEPARATOR)
        ));
    }
}
