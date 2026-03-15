<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Exceptions;

use RuntimeException;

class EventIOException extends RuntimeException
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public readonly ?string $errorBody = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
