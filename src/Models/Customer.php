<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class Customer
{
    /**
     * @param list<Group>|null $groups
     */
    public function __construct(
        public int $id,
        public string $fullName,
        public string $emailAddress,
        public ?string $postCode = null,
        public ?array $groups = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            fullName: $data['full_name'],
            emailAddress: $data['email_address'],
            postCode: $data['post_code'] ?? null,
            groups: isset($data['groups']) ? array_values(array_map(Group::fromArray(...), $data['groups'])) : null,
        );
    }
}
