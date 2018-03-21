<?php

namespace webignition\InternetMediaType\Parameter;

use webignition\InternetMediaTypeInterface\ParameterInterface;

class Parameter implements ParameterInterface
{
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
        $this->setAttribute($attribute);
        $this->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($attribute)
    {
        $this->attribute = trim(strtolower($attribute));
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if (empty($this->attribute)) {
            return '';
        }

        if (empty($this->value)) {
            return $this->attribute;
        }

        return $this->attribute . self::ATTRIBUTE_VALUE_SEPARATOR . $this->value;
    }
}
