<?php

namespace webignition\InternetMediaType\Parser;

/**
 * Attempts to fix unparseable internet media types based purely on
 * observed invalid media type strings that, upon visual observation, can
 * be translated into something sensible
 */
class TypeFixer
{
    const COMMA_SEPARATED_TYPE_SEPARATOR = ', ';
    const TYPE_SUBTYPE_SEPARATOR = '/';

    /**
     * @var string
     */
    private $inputString;

    /**
     * @var int
     */
    private $position;

    public function setInputString(string $inputString)
    {
        $this->inputString = $inputString;
    }

    public function setPosition(int $position)
    {
        $this->position = $position;
    }

    public function fix(): ?string
    {
        return $this->getLongestString(array(
            $this->commaSeparatedTypeFix(),
            $this->spaceSeparatingTypeAndAttributeFix()
        ));
    }

    /**
     * Attempt to fix media types that are formatted as:
     *
     * type/subtype, type/subtype
     *
     * i.e. two media types comma-separated together
     *
     * If of this type, go for the longest valid option
     */
    private function commaSeparatedTypeFix(): ?string
    {
        if ($this->position === 0) {
            return null;
        }

        $separatorComparator = substr($this->inputString, $this->position - 1, 2);
        if ($separatorComparator !== self::COMMA_SEPARATED_TYPE_SEPARATOR) {
            return null;
        }

        $possibleTypeSubtypes = array(
            substr($this->inputString, 0, $this->position - 1),
            substr($this->inputString, $this->position + 1)
        );

        $typeSubtypes = array();

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
     *
     * @return string
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
            $typeParser = new TypeParser();
            $type = $typeParser->parse($possibleTypeSubtype);

            $subtypeParser = new SubtypeParser();
            $subtype = $subtypeParser->parse($possibleTypeSubtype);

            return $type . self::TYPE_SUBTYPE_SEPARATOR . $subtype;
        } catch (\Exception $ex) {
            return null;
        }
    }

    /**
     * Attempt to fix media types that are formatted as:
     *
     * type/subtype attribute=value
     *
     * i.e. a media type and parameters separated by a space not a semicolon
     */
    private function spaceSeparatingTypeAndAttributeFix(): ?string
    {
        if ($this->position === 0) {
            return null;
        }

        if ($this->inputString[$this->position] !== ' ') {
            return null;
        }

        return $this->getTypeSubtypeFromPossibleTypeSubtype(
            trim(substr(
                $this->inputString,
                0,
                $this->position
            ), "\t\n\r\0\x0B,") . ';' . substr($this->inputString, $this->position + 1)
        );
    }
}
