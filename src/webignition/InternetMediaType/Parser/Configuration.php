<?php

namespace webignition\InternetMediaType\Parser;

class Configuration
{
    /**
     * @var boolean
     */
    private $ignoreInvalidAttributes = false;

    /**
     * @var boolean
     */
    private $attemptToRecoverFromInvalidInternalCharacter = false;

    /**
     * @return self
     */
    public function enableIgnoreInvalidAttributes()
    {
        $this->ignoreInvalidAttributes = true;

        return $this;
    }

    /**
     * @return self
     */
    public function disableIgnoreInvalidAttributes()
    {
        $this->ignoreInvalidAttributes = false;

        return $this;
    }

    /**
     * @return boolean
     */
    public function ignoreInvalidAttributes()
    {
        return $this->ignoreInvalidAttributes;
    }

    /**
     * @return self
     */
    public function enableAttemptToRecoverFromInvalidInternalCharacter()
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = true;

        return $this;
    }

    /**
     * @return self
     */
    public function disableAttemptToRecoverFromInvalidInternalCharacter()
    {
        $this->attemptToRecoverFromInvalidInternalCharacter = false;

        return $this;
    }

    /**
     * @return boolean
     */
    public function attemptToRecoverFromInvalidInternalCharacter()
    {
        return $this->attemptToRecoverFromInvalidInternalCharacter;
    }
}
