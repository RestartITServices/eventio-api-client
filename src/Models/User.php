<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class User implements \JsonSerializable
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

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'event_role' => $this->eventRole,
        ];
    }
}
