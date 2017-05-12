<?php

namespace webignition\InternetMediaType\Parameter;

/**
 * A parameter value present in an Internet media type
 *
 * If media type == 'text/html; charset=UTF8', parameter == 'charset=UTF8'
 *
 * Defined as:
 *
 * parameter               = attribute "=" value
 * attribute               = token
 * value                   = token | quoted-string
 *
 * The type, subtype, and parameter attribute names are case-insensitive
 *
 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec3.html#sec3.6
 *
 */
class Parameter
{
    const ATTRIBUTE_VALUE_SEPARATOR = '=';

    /**
     * The parameter attribute.
     *
     * For a parameter of 'charset=UTF8', this woud be 'charset'
     *
     * @var string
     */
    private $attribute;

    /**
     * The parameter value
     *
     * For a parameter of 'charset=UTF8', this would be 'UTF8'
     *
     * @var string
     */
    private $value;

    /**
     * @param string$attribute
     * @param string|null $value
     */
    public function __construct($attribute, $value = null)
    {
        $this->attribute = trim(strtolower($attribute));
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (empty($this->attribute)) {
            return '';
        }

        if (empty($this->value)) {
            return $this->getAttribute();
        }

        return $this->getAttribute() . self::ATTRIBUTE_VALUE_SEPARATOR . $this->getValue();
    }
}
