<?php

namespace webignition\InternetMediaType;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\InternetMediaTypeInterface\ParameterInterface;

class InternetMediaType implements InternetMediaTypeInterface, \Stringable
{
    /**
     * For a 'text/html' media type, this would be 'text'.
     */
    private ?string $type = null;

    /**
     * For a 'text/html' media type, this would be 'html'.
     */
    private ?string $subtype = null;

    /**
     * @var ParameterInterface[]
     */
    private array $parameters = [];

    /**
     * @param array<mixed> $parameters
     */
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

    public function __toString(): string
    {
        $string = $this->getTypeSubtypeString();

        if (0 === count($this->getParameters())) {
            return $string;
        }

        $parameterStringParts = [];

        foreach ($this->getParameters() as $parameter) {
            $parameterStringParts[] = (string) $parameter;
        }

        if (!$this->isEmptyParameterStringCollection($parameterStringParts)) {
            $string .= self::ATTRIBUTE_PARAMETER_SEPARATOR
                . ' '
                . implode(self::ATTRIBUTE_PARAMETER_SEPARATOR . ' ', $parameterStringParts);
        }

        return trim($string);
    }

    public function setType(string $type): void
    {
        $this->type = strtolower($type);
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setSubtype(string $subtype): void
    {
        $this->subtype = strtolower($subtype);
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function addParameter(ParameterInterface $parameter): void
    {
        $this->parameters[$parameter->getAttribute()] = $parameter;
    }

    public function hasParameter(string $attribute): bool
    {
        return !is_null($this->getParameter($attribute));
    }

    public function removeParameter(ParameterInterface $parameter): void
    {
        if ($this->hasParameter($parameter->getAttribute())) {
            unset($this->parameters[$parameter->getAttribute()]);
        }
    }

    public function getParameter(string $attribute): ?ParameterInterface
    {
        $attribute = trim(strtolower($attribute));

        return $this->parameters[$attribute] ?? null;
    }

    /**
     * @return ParameterInterface[]
     */
    public function getParameters(): array
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

    /**
     * @param string[] $parameterStringCollection
     */
    private function isEmptyParameterStringCollection(array $parameterStringCollection): bool
    {
        foreach ($parameterStringCollection as $value) {
            if ('' != $value) {
                return false;
            }
        }

        return true;
    }
}
