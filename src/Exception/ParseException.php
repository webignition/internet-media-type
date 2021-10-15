<?php

namespace webignition\InternetMediaType\Exception;

class ParseException extends \Exception
{
    public const CODE_ATTRIBUTE_ERROR = 1;
    public const CODE_TYPE_ERROR = 2;
    public const CODE_SUBTYPE_ERROR = 3;

    private const EXCEPTION_CODE_MAP = [
        AttributeParserException::class => self::CODE_ATTRIBUTE_ERROR,
        TypeParserException::class => self::CODE_TYPE_ERROR,
        SubtypeParserException::class => self::CODE_SUBTYPE_ERROR,
    ];

    public function __construct(
        private string $contentTypeString,
        private ComponentExceptionInterface $componentException,
    ) {
        parent::__construct(
            $componentException->getMessage(),
            self::EXCEPTION_CODE_MAP[$componentException::class] ?? 0,
            $componentException
        );
    }

    public function getContentTypeString(): string
    {
        return $this->contentTypeString;
    }

    public function getComponentException(): ComponentExceptionInterface
    {
        return $this->componentException;
    }
}
