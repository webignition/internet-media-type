<?php

namespace webignition\Tests\InternetMediaType\Parser;

use webignition\InternetMediaType\Parser\Configuration;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Configuration
     */
    private $configuration;

    protected function setUp()
    {
        parent::setUp();
        $this->configuration = new Configuration();
    }

    public function testDefaults()
    {
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());
    }

    public function testToggleIgnoreInvalidAttributes()
    {
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());

        $this->configuration->enableIgnoreInvalidAttributes();
        $this->assertTrue($this->configuration->ignoreInvalidAttributes());

        $this->configuration->disableIgnoreInvalidAttributes();
        $this->assertFalse($this->configuration->ignoreInvalidAttributes());
    }

    public function testToggleAttemptToRecoverFromInvalidInternalCharacter()
    {
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());

        $this->configuration->enableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertTrue($this->configuration->attemptToRecoverFromInvalidInternalCharacter());

        $this->configuration->disableAttemptToRecoverFromInvalidInternalCharacter();
        $this->assertFalse($this->configuration->attemptToRecoverFromInvalidInternalCharacter());
    }
}
