<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\Exception\AttributeParserException;
use webignition\InternetMediaType\Parameter;
use webignition\InternetMediaTypeInterface\ParameterInterface;
use webignition\QuotedString\Exception as QuotedStringException;
use webignition\StringParser\UnknownStateException;

/**
 * Parses a parameter string value into a Parameter object.
 *
 * Defined as:
 *
 * parameter               = attribute "=" value
 * attribute               = token
 * value                   = token | quoted-string
 *
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.6
 *
 * Linear white space (LWS) MUST NOT be used between the type and subtype, nor between an attribute and its value.
 *
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.7
 */
class ParameterParser
{
    public function __construct(
        private AttributeParser $attributeParser,
        private ValueParser $valueParser,
    ) {
    }

    /**
     * @throws AttributeParserException
     * @throws UnknownStateException
     * @throws QuotedStringException
     */
    public function parse(string $parameterString): ParameterInterface
    {
        $inputString = trim($parameterString);
        $attribute = $this->attributeParser->parse($inputString);

        if ('' === $attribute) {
            return new Parameter('', '');
        }

        $value = $this->valueParser->parse($parameterString, $attribute);

        return new Parameter($attribute, $value);
    }
}
