<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parameter;
use webignition\InternetMediaType\Parser\Configuration;
use webignition\InternetMediaTypeInterface\ParameterInterface;

/**
 * Parses a parameter string value into a Parameter object
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
 *
 */
class Parser
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration();
    }

    public function setConfiguration(Configuration  $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * @param string $parameterString
     *
     * @return ParameterInterface
     *
     * @throws AttributeParserException
     */
    public function parse(string $parameterString): ParameterInterface
    {
        $inputString = trim($parameterString);
        $attribute = $this->createAttributeParser()->parse($inputString);

        if (empty($attribute)) {
            return new Parameter('', '');
        }

        $value = $this->createValueParser($attribute)->parse($parameterString);

        $parameter = new Parameter($attribute, $value);

        return $parameter;
    }

    private function createAttributeParser(): AttributeParser
    {
        $attributeParser = new AttributeParser();
        $attributeParser->setConfiguration($this->getConfiguration());

        return $attributeParser;
    }

    private function createValueParser(string $attribute): ValueParser
    {
        $valueParser = new ValueParser();
        $valueParser->setAttribute($attribute);
        return $valueParser;
    }
}
