<?php

declare(strict_types=1);

namespace Kaizen\Components\HttpKernel\HttpFoundation;

readonly class JsonResponse extends Response
{
    /**
     * @param array<int|string, mixed> $content
     */
    public function __construct(
        array $content = [],
        ResponseCode $code = ResponseCode::HTTP_OK
    ) {
        $serializedContent = json_encode($content, JSON_THROW_ON_ERROR);

        parent::__construct(
            content: $serializedContent,
            code: $code
        );
    }
}
