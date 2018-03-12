<?php

namespace webignition\Tests\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parameter\Parser\AttributeParserException;
use webignition\Tests\InternetMediaType\BaseTest;

class AttributeParserExceptionTest extends BaseTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $message
     * @param int $code
     * @param int $position
     * @param bool $expectedIsInternalCharacterException
     * @param int $expectedPosition
     */
    public function testCreate($message, $code, $position, $expectedIsInternalCharacterException, $expectedPosition)
    {
        $exception = new AttributeParserException($message, $code, $position);

        $this->assertEquals($expectedIsInternalCharacterException, $exception->isInvalidInternalCharacterException());
        $this->assertEquals($expectedPosition, $exception->getPosition());
    }

    /**
     * @return array
     */
    public function createDataProvider()
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
