<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?EventRole $eventRole = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            eventRole: isset($data['event_role']) ? EventRole::fromArray($data['event_role']) : null,
        );
    }
}
