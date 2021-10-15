<?php

namespace webignition\Tests\InternetMediaType\Exception;

use PHPUnit\Framework\TestCase;
use webignition\InternetMediaType\Exception\SubtypeParserException;

class SubtypeParserExceptionTest extends TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        string $message,
        int $code,
        int $position,
        bool $expectedIsInternalCharacterException,
        int $expectedPosition
    ): void {
        $exception = new SubtypeParserException($message, $code, $position);

        $this->assertEquals($expectedIsInternalCharacterException, $exception->isInvalidInternalCharacterException());
        $this->assertEquals($expectedPosition, $exception->getPosition());
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'is internal character exception' => [
                'message' => '',
                'code' => SubtypeParserException::INTERNAL_INVALID_CHARACTER_CODE,
                'position' => 12,
                'expectedIsInternalCharacterException' => true,
                'expectedPosition' => 12,
            ],
            'not internal character exception' => [
                'message' => '',
                'code' => 2,
                'position' => 3,
                'expectedIsInternalCharacterException' => false,
                'expectedPosition' => 3,
            ],
        ];
    }

    public function testSetPosition(): void
    {
        $initialPosition = 3;
        $newPosition = 4;

        $exception = new SubtypeParserException(
            '',
            SubtypeParserException::INTERNAL_INVALID_CHARACTER_CODE,
            $initialPosition
        );

        $this->assertEquals($initialPosition, $exception->getPosition());

        $exception->setPosition($newPosition);
        $this->assertEquals($newPosition, $exception->getPosition());
    }
}
