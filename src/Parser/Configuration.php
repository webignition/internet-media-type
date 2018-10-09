<?php

namespace webignition\InternetMediaType\Parser;

class Configuration
{
    /**
     * @var bool
     */
    private $ignoreInvalidAttributes = false;

    /**
     * @var bool
     */
    private $attemptToRecoverFromInvalidInternalCharacter = false;

    public function enableIgnoreInvalidAttributes()
    {
        $this->ignoreInvalidAttributes = true;
    }

    public function disableIgnoreInvalidAttributes()
    {
        $this->ignoreInvalidAttributes = false;
    }

    public function ignoreInvalidAttributes(): bool
    {
        return $this->ignoreInvalidAttributes;
    }

    public function enableAttemptToRecoverFromInvalidInternalCharacter()
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = true;
    }

    public function disableAttemptToRecoverFromInvalidInternalCharacter()
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = false;
    }

    public function attemptToRecoverFromInvalidInternalCharacter(): bool
    {
        return $this->attemptToRecoverFromInvalidInternalCharacter;
    }
}
