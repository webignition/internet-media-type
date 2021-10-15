<?php

namespace webignition\InternetMediaType\Exception;

class SubtypeParserException extends \Exception implements ComponentExceptionInterface
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
        return self::INTERNAL_INVALID_CHARACTER_CODE === $this->getCode();
    }
}
