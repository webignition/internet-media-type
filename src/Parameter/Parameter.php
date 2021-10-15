<?php

namespace webignition\InternetMediaType\Parameter;

use webignition\InternetMediaTypeInterface\ParameterInterface;

class Parameter implements ParameterInterface, \Stringable
{
    /**
     * For a parameter of 'charset=UTF8', this woud be 'charset'.
     */
    private string $attribute;

    /**
     * For a parameter of 'charset=UTF8', this would be 'UTF8'.
     */
    private ?string $value;

    public function __construct(string $attribute, ?string $value = null)
    {
        $this->attribute = trim(strtolower($attribute));
        $this->value = $value;
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

    public function getAttribute(): string
    {
        return $this->attribute;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
