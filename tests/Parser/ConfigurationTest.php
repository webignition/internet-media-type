<?php

namespace webignition\Tests\InternetMediaType\Parser;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Parser\Configuration;

class ConfigurationTest extends TestCase
{
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configuration = new Configuration();
    }

    public function testDefaults(): void
    {
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());
    }

    public function testToggleIgnoreInvalidAttributes(): void
    {
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());

        $this->configuration->enableIgnoreInvalidAttributes();
        $this->assertTrue($this->configuration->ignoreInvalidAttributes());

        $this->configuration->disableIgnoreInvalidAttributes();
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());
    }

    public function testToggleAttemptToRecoverFromInvalidInternalCharacter(): void
    {
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());

        $this->configuration->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertTrue($this->configuration->attemptToRecoverFromInvalidInternalCharacter());

        $this->configuration->disableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());
    }
}
