<?php

namespace webignition\InternetMediaType;

use webignition\InternetMediaType\Parameter\Parameter;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\InternetMediaTypeInterface\ParameterInterface;

class InternetMediaType implements InternetMediaTypeInterface
{
    /**
     * Main media type.
     *
     * For a 'text/html' media type, this would be 'text'
     *
     * @var string
     */
    private $type = null;

    /**
     * Subtype, a type within a type
     *
     * For a 'text/html' media type, this would be 'html'
     *
     * @var string
     */
    private $subtype = null;

    /**
     * Collection of Parameter objects
     *
     * @var Parameter[]
     */
    private $parameters = [];

    public function __construct(?string $type = null, ?string $subtype = null, array $parameters = [])
    {
        if (!empty($type)) {
            $this->setType($type);
        }

        if (!empty($subtype)) {
            $this->setSubtype($subtype);
        }

        foreach ($parameters as $parameter) {
            if ($parameter instanceof ParameterInterface) {
                $this->addParameter($parameter);
            }
        }
    }

    public function setType(string $type)
    {
        $this->type = strtolower($type);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setSubtype(string $subtype)
    {
        $this->subtype = strtolower($subtype);
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function addParameter(ParameterInterface $parameter)
    {
        $this->parameters[$parameter->getAttribute()] = $parameter;
    }

    public function hasParameter(string $attribute): bool
    {
        return !is_null($this->getParameter($attribute));
    }

    public function removeParameter(ParameterInterface $parameter)
    {
        if ($this->hasParameter($parameter->getAttribute())) {
            unset($this->parameters[$parameter->getAttribute()]);
        }
    }

    public function getParameter(string $attribute): ?ParameterInterface
    {
        $attribute = trim(strtolower($attribute));

        return isset($this->parameters[$attribute]) ? $this->parameters[$attribute] : null;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getTypeSubtypeString(): string
    {
        $string = '';

        if (empty($this->type)) {
            return $string;
        }

        if (empty($this->subtype)) {
            return $string;
        }

        return $this->type . self::TYPE_SUBTYPE_SEPARATOR . $this->subtype;
    }

    public function __toString(): string
    {
        $string = $this->getTypeSubtypeString();

        if (count($this->getParameters()) === 0) {
            return $string;
        }

        $parameterStringParts = [];

        foreach ($this->getParameters() as $parameter) {
            $parameterStringParts[] = (string)$parameter;
        }

        if (!$this->isEmptyParameterStringCollection($parameterStringParts)) {
            $string .= self::ATTRIBUTE_PARAMETER_SEPARATOR
                . ' '
                . implode(self::ATTRIBUTE_PARAMETER_SEPARATOR . ' ', $parameterStringParts);
        }

        return trim($string);
    }

    /**
     * @param string[] $parameterStringCollection
     *
     * @return bool
     */
    private function isEmptyParameterStringCollection(array $parameterStringCollection): bool
    {
        foreach ($parameterStringCollection as $value) {
            if ($value != '') {
                return false;
            }
        }

        return true;
    }
}
