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

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = strtolower($type);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubtype($subtype)
    {
        $this->subtype = strtolower($subtype);
    }

    /**
     * {@inheritdoc}
     */
    public function getSubtype()
    {
        return $this->subtype;
    }

    /**
     * {@inheritdoc}
     */
    public function addParameter(ParameterInterface $parameter)
    {
        $this->parameters[$parameter->getAttribute()] = $parameter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter($attribute)
    {
        return !is_null($this->getParameter($attribute));
    }

    /**
     * {@inheritdoc}
     */
    public function removeParameter(ParameterInterface $parameter)
    {
        if ($this->hasParameter($parameter->getAttribute())) {
            unset($this->parameters[$parameter->getAttribute()]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter($attribute)
    {
        $attribute = trim(strtolower($attribute));

        return isset($this->parameters[$attribute]) ? $this->parameters[$attribute] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getTypeSubtypeString()
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
     * @return string
     */
    public function __toString()
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
    private function isEmptyParameterStringCollection($parameterStringCollection)
    {
        foreach ($parameterStringCollection as $value) {
            if ($value != '') {
                return false;
            }
        }

        return true;
    }
}
