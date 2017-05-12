<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parameter;
use webignition\InternetMediaType\Parser\Configuration;

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

    /**
     * @param Configuration $configuration
     * @return self
     */
    public function setConfiguration(Configuration  $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        if (is_null($this->configuration)) {
            $this->configuration = new Configuration();
        }

        return $this->configuration;
    }

    /**
     * @param string $parameterString
     *
     * @return Parameter
     */
    public function parse($parameterString)
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

    /**
     * @return AttributeParser
     */
    private function createAttributeParser()
    {
        $attributeParser = new AttributeParser();
        $attributeParser->setConfiguration($this->getConfiguration());

        return $attributeParser;
    }

    /**
     * @param string $attribute
     *
     * @return ValueParser
     */
    private function createValueParser($attribute)
    {
        $valueParser = new ValueParser();
        $valueParser->setAttribute($attribute);
        return $valueParser;
    }
}
