<?php

namespace webignition\InternetMediaType\Parser;

use Throwable;

class ParseException extends \Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        private string $contentTypeString = '',
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getContentTypeString(): string
    {
        return $this->contentTypeString;
    }
}
