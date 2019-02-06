<?php

namespace webignition\InternetMediaType\Parser;

use Throwable;

class ParseException extends \Exception
{
    /**
     * @var string
     */
    private $contentTypeString;

    public function __construct(
        string $message = '',
        int $code = 0,
        string $contentTypeString = '',
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->contentTypeString = $contentTypeString;
    }

    public function getContentTypeString(): string
    {
        return $this->contentTypeString;
    }
}
