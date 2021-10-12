<?php

namespace webignition\InternetMediaType\Parser;

class Configuration
{
    private bool $ignoreInvalidAttributes = false;

    private bool $attemptToRecoverFromInvalidInternalCharacter = false;

    public function enableIgnoreInvalidAttributes(): void
    {
        $this->ignoreInvalidAttributes = true;
    }

    public function disableIgnoreInvalidAttributes(): void
    {
        $this->ignoreInvalidAttributes = false;
    }

    public function ignoreInvalidAttributes(): bool
    {
        return $this->ignoreInvalidAttributes;
    }

    public function enableAttemptToRecoverFromInvalidInternalCharacter(): void
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = true;
    }

    public function disableAttemptToRecoverFromInvalidInternalCharacter(): void
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = false;
    }

    public function attemptToRecoverFromInvalidInternalCharacter(): bool
    {
        return $this->attemptToRecoverFromInvalidInternalCharacter;
    }
}
