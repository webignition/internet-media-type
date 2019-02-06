<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parameter\Parser\Parser as ParameterParser;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\InternetMediaTypeInterface\ParameterInterface;

/**
 * Parses a string representation of an Internet media type into an
 * InternetMediaTypeInterface object
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

    public function __construct()
    {
        $this->configuration = new Configuration();
        $this->typeParser = new TypeParser();

        $this->subtypeParser = new SubtypeParser();
        $this->subtypeParser->setConfiguration($this->configuration);

        $this->parameterParser = new ParameterParser();
        $this->parameterParser->setConfiguration($this->configuration);
    }

    /**
     * @param string $internetMediaTypeString
     *
     * @return InternetMediaTypeInterface|null
     *
     * @throws ParseException
     */
    public function parse(string $internetMediaTypeString): ?InternetMediaTypeInterface
    {
        $inputString = trim($internetMediaTypeString);

        $internetMediaType = new InternetMediaType();

        try {
            $internetMediaType->setType($this->typeParser->parse($inputString));
            $internetMediaType->setSubtype($this->subtypeParser->parse($inputString));

            $parameterString = $this->createParameterString(
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
        } catch (TypeParserException $typeParserException) {
            throw new ParseException(
                $typeParserException->getMessage(),
                $typeParserException->getCode(),
                $inputString,
                $typeParserException
            );
        } catch (SubtypeParserException $subtypeParserException) {
            throw new ParseException(
                $subtypeParserException->getMessage(),
                $subtypeParserException->getCode(),
                $inputString,
                $subtypeParserException
            );
        } catch (AttributeParserException $attributeParserException) {
            throw new ParseException(
                $attributeParserException->getMessage(),
                $attributeParserException->getCode(),
                $inputString,
                $attributeParserException
            );
        }
    }

    /**
     * @param string $inputString
     * @param string $type
     * @param string $subtype
     *
     * @return string
     */
    private function createParameterString(string $inputString, string $type, string $subtype): string
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
     * @return string[]
     */
    private function getParameterStrings(string $parameterString): array
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
     * @return ParameterInterface[]
     *
     * @throws AttributeParserException
     */
    private function getParameters($parameterStrings): array
    {
        $parameters = array();
        foreach ($parameterStrings as $parameterString) {
            $parameters[] = $this->parameterParser->parse($parameterString);
        }

        return $parameters;
    }

    public function setConfiguration(Configuration  $configuration)
    {
        $this->configuration = $configuration;
        $this->subtypeParser->setConfiguration($configuration);
        $this->parameterParser->setConfiguration($configuration);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setIgnoreInvalidAttributes(bool $ignoreInvalidAttributes)
    {
        if (filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableIgnoreInvalidAttributes();
        } else {
            $this->getConfiguration()->disableIgnoreInvalidAttributes();
        }
    }

    public function setAttemptToRecoverFromInvalidInternalCharacter(bool $attemptToRecoverFromInvalidInternalCharacter)
    {
        if (filter_var($attemptToRecoverFromInvalidInternalCharacter, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        } else {
            $this->getConfiguration()->disableAttemptToRecoverFromInvalidInternalCharacter();
        }
    }
}
