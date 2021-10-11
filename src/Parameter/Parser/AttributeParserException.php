<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parser\ParseException;

class AttributeParserException extends ParseException
{
    const INTERNAL_INVALID_CHARACTER_CODE = 1;

    /**
     * @var int
     */
    private $position;

    public function __construct(string $message, int $code, int $position)
    {
        parent::__construct($message, $code);

        $this->position = $position;
    }

    public function isInvalidInternalCharacterException(): bool
    {
        return $this->getCode() === self::INTERNAL_INVALID_CHARACTER_CODE;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
