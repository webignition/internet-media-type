<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parameter;
use webignition\InternetMediaType\Parser\Configuration;
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
class Parser
{
    private Configuration $configuration;
    private AttributeParser $attributeParser;
    private ValueParser $valueParser;

    public function __construct()
    {
        $this->configuration = new Configuration();
        $this->attributeParser = new AttributeParser();
        $this->valueParser = new ValueParser();

        $this->attributeParser->setConfiguration($this->configuration);
    }

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
        $this->attributeParser->setConfiguration($configuration);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
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
