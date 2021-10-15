<?php

namespace webignition\InternetMediaType\Parser;

/**
 * Attempts to fix unparseable internet media types based purely on
 * observed invalid media type strings that, upon visual observation, can
 * be translated into something sensible.
 */
class TypeFixer
{
    public const COMMA_SEPARATED_TYPE_SEPARATOR = ', ';
    public const TYPE_SUBTYPE_SEPARATOR = '/';

    public function __construct(
        private TypeParser $typeParser,
        private SubtypeParser $subtypeParser,
    ) {
    }

    public function fix(string $input, int $position): ?string
    {
        $commaSeparatedTypeFix = $this->commaSeparatedTypeFix($input, $position);
        $spaceSeparatingTypeAndAttributeFix = $this->spaceSeparatingTypeAndAttributeFix($input, $position);

        if (null === $commaSeparatedTypeFix && null === $spaceSeparatingTypeAndAttributeFix) {
            return null;
        }

        return strlen((string) $commaSeparatedTypeFix) >= strlen((string) $spaceSeparatingTypeAndAttributeFix)
            ? $commaSeparatedTypeFix
            : $spaceSeparatingTypeAndAttributeFix;
    }

    /**
     * Attempt to fix media types that are formatted as:.
     *
     * type/subtype, type/subtype
     *
     * i.e. two media types comma-separated together
     *
     * If of this type, go for the longest valid option
     */
    private function commaSeparatedTypeFix(string $input, int $position): ?string
    {
        if (0 === $position) {
            return null;
        }

        $separatorComparator = substr($input, $position - 1, 2);
        if (self::COMMA_SEPARATED_TYPE_SEPARATOR !== $separatorComparator) {
            return null;
        }

        $possibleTypeSubtypes = [
            substr($input, 0, $position - 1),
            substr($input, $position + 1)
        ];

        $typeSubtypes = [];

        foreach ($possibleTypeSubtypes as $possibleTypeSubtype) {
            $typeSubtype = $this->getTypeSubtypeFromPossibleTypeSubtype($possibleTypeSubtype);

            if (!in_array($typeSubtype, $typeSubtypes) && !is_null($typeSubtype)) {
                $typeSubtypes[] = $typeSubtype;
            }
        }

        return $this->getLongestString($typeSubtypes);
    }

    /**
     * @param string[] $strings
     */
    private function getLongestString(array $strings): ?string
    {
        return array_reduce($strings, function ($a, $b) {
            return strlen($a) > strlen($b) ? $a : $b;
        });
    }

    private function getTypeSubtypeFromPossibleTypeSubtype(string $possibleTypeSubtype): ?string
    {
        try {
            $type = $this->typeParser->parse($possibleTypeSubtype);
            $subtype = $this->subtypeParser->parse($possibleTypeSubtype);

            return $type . self::TYPE_SUBTYPE_SEPARATOR . $subtype;
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Attempt to fix media types that are formatted as:.
     *
     * type/subtype attribute=value
     *
     * i.e. a media type and parameters separated by a space not a semicolon
     */
    private function spaceSeparatingTypeAndAttributeFix(string $input, int $position): ?string
    {
        if (0 === $position) {
            return null;
        }

        if (' ' !== $input[$position]) {
            return null;
        }

        return $this->getTypeSubtypeFromPossibleTypeSubtype(
            trim(substr(
                $input,
                0,
                $position
            ), "\t\n\r\0\x0B,") . ';' . substr($input, $position + 1)
        );
    }
}
