<?php

namespace webignition\InternetMediaType\Parameter\Parser;

use webignition\InternetMediaType\Parser\ParseException;

class AttributeParserException extends ParseException
{
    const INTERNAL_INVALID_CHARACTER_CODE = 1;

    /**
     * @var int
     */
    private $position;

    /**
     * @param string $message
     * @param int $code
     * @param int $position
     * @param \Exception|null $previous
     */
    public function __construct($message, $code, $position, $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->position = $position;
    }

    /**
     * @return boolean
     */
    public function isInvalidInternalCharacterException()
    {
        return $this->getCode() === self::INTERNAL_INVALID_CHARACTER_CODE;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }
}
