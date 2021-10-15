<?php

namespace webignition\InternetMediaType\Parser;

use webignition\InternetMediaType\InternetMediaType;
use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\InternetMediaType\Parameter\Parser\Parser as ParameterParser;
use webignition\InternetMediaTypeInterface\InternetMediaTypeInterface;
use webignition\InternetMediaTypeInterface\ParameterInterface;
use webignition\QuotedString\Exception as QuotedStringException;
use webignition\StringParser\UnknownStateException;

/**
 * Parses a string representation of an Internet media type into an
 * InternetMediaTypeInterface object.
 */
class Parser
{
    public const TYPE_SUBTYPE_SEPARATOR = '/';
    public const TYPE_PARAMETER_SEPARATOR = ';';

    private TypeParser $typeParser;
    private SubtypeParser $subtypeParser;
    private ParameterParser $parameterParser;
    private Configuration $configuration;
    private bool $hasAttemptedToFixAttributeInvalidInternalCharacter = false;

    public function __construct()
    {
        $this->configuration = new Configuration();
        $this->typeParser = new TypeParser();

        $this->subtypeParser = new SubtypeParser();

        $this->parameterParser = new ParameterParser();
        $this->parameterParser->setConfiguration($this->configuration);
    }

    /**
     * @throws ParseException
     * @throws QuotedStringException
     * @throws UnknownStateException
     */
    public function parse(string $internetMediaTypeString): ?InternetMediaTypeInterface
    {
        $inputString = trim($internetMediaTypeString);

        $internetMediaType = new InternetMediaType();

        try {
            $internetMediaType->setType($this->typeParser->parse($inputString));
            $internetMediaType->setSubtype($this->parseSubtype($inputString));

            $parameterString = $this->createParameterString(
                $inputString,
                (string) $internetMediaType->getType(),
                (string) $internetMediaType->getSubtype()
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

    public function setConfiguration(Configuration $configuration): void
    {
        $this->configuration = $configuration;
        $this->parameterParser->setConfiguration($configuration);
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setIgnoreInvalidAttributes(bool $ignoreInvalidAttributes): void
    {
        if (filter_var($ignoreInvalidAttributes, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableIgnoreInvalidAttributes();
        } else {
            $this->getConfiguration()->disableIgnoreInvalidAttributes();
        }
    }

    public function setAttemptToRecoverFromInvalidInternalCharacter(
        bool $attemptToRecoverFromInvalidInternalCharacter
    ): void {
        if (filter_var($attemptToRecoverFromInvalidInternalCharacter, FILTER_VALIDATE_BOOLEAN)) {
            $this->getConfiguration()->enableAttemptToRecoverFromInvalidInternalCharacter();
        } else {
            $this->getConfiguration()->disableAttemptToRecoverFromInvalidInternalCharacter();
        }
    }

    private function createParameterString(string $inputString, string $type, string $subtype): string
    {
        $parts = explode(self::TYPE_PARAMETER_SEPARATOR, $inputString, 2);

        if (1 === count($parts)) {
            return trim(str_replace($type . self::TYPE_SUBTYPE_SEPARATOR . $subtype, '', $inputString));
        }

        return trim($parts[1]);
    }

    /**
     * Get collection of string representations of each parameter.
     *
     * @return string[]
     */
    private function getParameterStrings(string $parameterString): array
    {
        $rawParameterStrings = explode(self::TYPE_PARAMETER_SEPARATOR, $parameterString);
        $parameterStrings = [];

        foreach ($rawParameterStrings as $rawParameterString) {
            if ('' != $rawParameterString) {
                $parameterStrings[] = trim($rawParameterString);
            }
        }

        return $parameterStrings;
    }

    /**
     * Get a collection of Parameter objects from a collection of string
     * representations of the same.
     *
     * @param string[] $parameterStrings
     *
     * @throws AttributeParserException
     * @throws QuotedStringException
     * @throws UnknownStateException
     *
     * @return ParameterInterface[]
     */
    private function getParameters(array $parameterStrings): array
    {
        $parameters = [];
        foreach ($parameterStrings as $parameterString) {
            $parameters[] = $this->parameterParser->parse($parameterString);
        }

        return $parameters;
    }

    /**
     * @throws SubtypeParserException
     * @throws UnknownStateException
     */
    private function parseSubtype(string $inputString): string
    {
        try {
            return $this->subtypeParser->parse($inputString);
        } catch (SubtypeParserException $subtypeParserException) {
            $shouldAttemptToFixInvalidInternalCharacter =
                $this->getConfiguration()->attemptToRecoverFromInvalidInternalCharacter()
                && !$this->hasAttemptedToFixAttributeInvalidInternalCharacter;

            if ($shouldAttemptToFixInvalidInternalCharacter) {
                $this->hasAttemptedToFixAttributeInvalidInternalCharacter = true;

                $fixer = new TypeFixer();
                $fixedType = $fixer->fix($inputString, $subtypeParserException->getPosition());

                if (is_string($fixedType)) {
                    return $this->subtypeParser->parse($fixedType);
                }
            }

            throw $subtypeParserException;
        }
    }
}
