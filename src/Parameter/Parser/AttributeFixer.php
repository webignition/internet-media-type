<?php

namespace webignition\InternetMediaType\Parameter\Parser;

/**
 * Attempts to fix unparseable internet media types based purely on
 * observed invalid media type strings that, upon visual observation, can
 * be translated into something sensible.
 */
class AttributeFixer
{
    public const COMMA_SEPARATED_TYPE_SEPARATOR = ', ';

    private string $inputString;

    public function setInputString(string $inputString): void
    {
        $this->inputString = $inputString;
    }

    public function fix(): string
    {
        $fixedString = $this->inputString;

        if ($this->isInvalid($this->inputString)) {
            $fixedString = $this->colonSeparatedAttributeValueFix($this->inputString);
        }

        return $fixedString;
    }

    private function isInvalid(string $parameterString): bool
    {
        try {
            $parser = new AttributeParser();
            $parser->parse($parameterString);
        } catch (\Exception) {
            return true;
        }

        return false;
    }

    /**
     * Attempt to fix a parameter string that incorrectly uses a colon as
     * the attribute-value separator instead of the equals sign.
     *
     * Invalid form "attribute: value"
     * Correct form "attribute=value"
     *
     * Attempt to translate invalid form into correct form
     */
    private function colonSeparatedAttributeValueFix(string $parameterString): string
    {
        if (!preg_match('/.+:\s+.+/', $parameterString)) {
            return $parameterString;
        }

        return (string) preg_replace('/:\s+/', '=', $parameterString);
    }
}
