<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parameter\Parser\Parser as ParameterParser;

/**
 * Parses a string representation of an Internet media type into an
 * InternetMediaType object
 */
class Parser
{
    const TYPE_SUBTYPE_SEPARATOR = '/';
    const TYPE_PARAMETER_SEPARATOR = ';';

    /**
     * @var TypeParser
     */
    private $typeParser = null;

    /**
     * @var SubtypeParser
     */
    private $subtypeParser = null;

    /**
     * @var ParameterParser
     */
    private $parameterParser = null;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param string $internetMediaTypeString
     *
     * @return InternetMediaType
     */
    public function parse($internetMediaTypeString)
    {
        $inputString = trim($internetMediaTypeString);

        $internetMediaType = new InternetMediaType();
        $internetMediaType->setType($this->getTypeParser()->parse($inputString));
        $internetMediaType->setSubtype($this->getSubypeParser()->parse($inputString));

        $parameterString = $this->getParameterString(
            $inputString,
            $internetMediaType->getType(),
            $internetMediaType->getSubtype()
        );
        $parameterStrings = $this->getParameterStrings($parameterString);

        $parameters = $this->getParameters($parameterStrings);

        foreach ($parameters as $parameter) {
            $internetMediaType->addParameter($parameter);
        }

        return $internetMediaType;
    }

    /**
     * @return TypeParser
     */
    private function getTypeParser()
    {
        if (is_null($this->typeParser)) {
            $this->typeParser = new TypeParser();
        }

        return $this->typeParser;
    }

    /**
     * @return SubtypeParser
     */
    private function getSubypeParser()
    {
        if (is_null($this->subtypeParser)) {
            $this->subtypeParser = new SubtypeParser();
            $this->subtypeParser->setConfiguration($this->getConfiguration());
        }

        return $this->subtypeParser;
    }

    /**
     * @return ParameterParser
     */
    private function getParameterParser()
    {
        if (is_null($this->parameterParser)) {
            $this->parameterParser = new ParameterParser();
            $this->parameterParser->setConfiguration($this->getConfiguration());
        }

        return $this->parameterParser;
    }

    /**
     * @param string $inputString
     * @param string $type
     * @param string $subtype
     *
     * @return string
     */
    private function getParameterString($inputString, $type, $subtype)
    {
        $parts = explode(self::TYPE_PARAMETER_SEPARATOR, $inputString, 2);

        if (count($parts) === 1) {
            return trim(str_replace($type . self::TYPE_SUBTYPE_SEPARATOR . $subtype, '', $inputString));
        }

        return trim($parts[1]);
    }

    /**
     * Get collection of string representations of each parameter
     *
     * @param string $parameterString
     *
     * @return array
     */
    private function getParameterStrings($parameterString)
    {
        $rawParameterStrings = explode(self::TYPE_PARAMETER_SEPARATOR, $parameterString);
        $parameterStrings = array();

        foreach ($rawParameterStrings as $rawParameterString) {
            if ($rawParameterString != '') {
                $parameterStrings[] = trim($rawParameterString);
            }
        }

        return $parameterStrings;
    }

    /**
     * Get a collection of Parameter objects from a collection of string
     * representations of the same
     *
     * @param array $parameterStrings
     *
     * @return array
     */
    private function getParameters($parameterStrings)
    {
        $parameters = array();
        foreach ($parameterStrings as $parameterString) {
            $parameters[] = $this->getParameterParser()->parse($parameterString);
        }

        return $parameters;
    }

    /**
     * @param Configuration $configuration
     *
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
     * @param boolean $ignoreInvalidAttributes
     */
    public function setIgnoreInvalidAttributes($ignoreInvalidAttributes)
    {
        if (filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableIgnoreInvalidAttributes();
        } else {
            $this->getConfiguration()->disableIgnoreInvalidAttributes();
        }
    }

    /**
     * @param boolean $attemptToRecoverFromInvalidInternalCharacter
     */
    public function setAttemptToRecoverFromInvalidInternalCharacter($attemptToRecoverFromInvalidInternalCharacter)
    {
        if (filter_var($attemptToRecoverFromInvalidInternalCharacter, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        } else {
            $this->getConfiguration()->disableAttemptToRecoverFromInvalidInternalCharacter();
        }
    }
}
