<?php

namespace webignition\InternetMediaType;

use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\InternetMediaTypeInterface\ParameterInterface;

class InternetMediaType implements InternetMediaTypeInterface, \Stringable
{
    /**
     * For a 'text/html' media type, this would be 'text'.
     */
    private ?string $type;

    /**
     * For a 'text/html' media type, this would be 'html'.
     */
    private ?string $subtype;

    /**
     * @var ParameterInterface[]
     */
    private array $parameters = [];

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(?string $type = null, ?string $subtype = null, array $parameters = [])
    {
        $this->type = $type;
        $this->subtype = $subtype;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function withType(string $type): InternetMediaTypeInterface
    {
        $new = clone $this;
        $new->type = strtolower($type);

        return $new;
    }

    public function getSubtype(): ?string
    {
        return $this->subtype;
    }

    public function withSubtype(string $subtype): InternetMediaTypeInterface
    {
        $new = clone $this;
        $new->subtype = strtolower($subtype);

        return $new;
    }

    public function withParameter(ParameterInterface $parameter): InternetMediaTypeInterface
    {
        $new = clone $this;
        $new->addParameter($parameter);

        return $new;
    }

    public function hasParameter(string $attribute): bool
    {
        return !is_null($this->getParameter($attribute));
    }

    public function removeParameter(ParameterInterface $parameter): InternetMediaTypeInterface
    {
        $new = clone $this;
        if ($new->hasParameter($parameter->getAttribute())) {
            $new = clone $this;
            unset($new->parameters[$parameter->getAttribute()]);
        }

        return $new;
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

    private function addParameter(ParameterInterface $parameter): void
    {
        $this->parameters[$parameter->getAttribute()] = $parameter;
    }
}
