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
     * @var null|string
     */
    private $value;

    public function __construct(string $attribute, ?string $value = null)
    {
        $this->setAttribute($attribute);
        $this->setValue($value);
    }

    public function setAttribute(string $attribute): void
    {
        $this->attribute = trim(strtolower($attribute));
    }

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function __toString(): string
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
