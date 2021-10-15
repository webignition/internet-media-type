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

    public function fix(string $input): string
    {
        return $this->colonSeparatedAttributeValueFix($input);
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
        return (string) preg_replace('/:\s+/', '=', $parameterString);
    }
}
