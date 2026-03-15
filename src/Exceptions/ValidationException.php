<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Exceptions;

class ValidationException extends EventIOException
{
    /**
     * @param array<string, list<string>> $errors
     */
    public function __construct(
        string $message = '',
        int $code = 422,
        ?\Throwable $previous = null,
        ?string $errorBody = null,
        public readonly array $errors = [],
    ) {
        parent::__construct($message, $code, $previous, $errorBody);
    }
}
