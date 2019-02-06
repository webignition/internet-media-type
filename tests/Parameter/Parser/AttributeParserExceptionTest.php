<?php
/** @noinspection PhpDocSignatureInspection */

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;

class AttributeParserExceptionTest extends \PHPUnit\Framework\TestCase
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
    ) {
        $exception = new AttributeParserException($message, $code, $position);

        $this->assertEquals($expectedIsInternalCharacterException, $exception->isInvalidInternalCharacterException());
        $this->assertEquals($expectedPosition, $exception->getPosition());
    }

    public function createDataProvider(): array
    {
        return [
            'is internal character exception' => [
                'message' => '',
                'code' => AttributeParserException::INTERNAL_INVALID_CHARACTER_CODE,
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

    public function testSetPosition()
    {
        $initialPosition = 3;
        $newPosition = 4;

        $exception = new AttributeParserException(
            '',
            AttributeParserException::INTERNAL_INVALID_CHARACTER_CODE,
            $initialPosition
        );

        $this->assertEquals($initialPosition, $exception->getPosition());

        $exception->setPosition($newPosition);
        $this->assertEquals($newPosition, $exception->getPosition());
    }
}
