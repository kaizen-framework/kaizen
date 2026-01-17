<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\HttpFoundation;

readonly class Response
{
    public function __construct(
        private string $content = '',
        private ResponseCode $code = ResponseCode::HTTP_OK
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    public function getResponseCode(): ResponseCode
    {
        return $this->code;
    }

    public function send(): void
    {
        header('Content-Type: text/html');
        echo $this->getContent();
    }
}
