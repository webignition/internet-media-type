<?php

namespace webignition\InternetMediaType\Parser;

class SubtypeParserException extends ParseException
{
    public const INTERNAL_INVALID_CHARACTER_CODE = 1;

    public function __construct(string $message, int $code, private int $position)
    {
        parent::__construct($message, $code);
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function isInvalidInternalCharacterException(): bool
    {
        return $this->getCode() === self::INTERNAL_INVALID_CHARACTER_CODE;
    }
}
