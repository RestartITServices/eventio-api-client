<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use EventIO\ApiClient\Enums\GroupUserRole;

final readonly class GroupUser implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $fullName,
        public ?string $firstName,
        public ?string $lastName,
        public string $emailAddress,
        public ?string $phoneNumber = null,
        public ?GroupUserRole $role = null,
        public bool $active = true,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            fullName: $data['full_name'],
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            emailAddress: $data['email_address'],
            phoneNumber: $data['phone_number'] ?? null,
            role: isset($data['role']) ? GroupUserRole::from($data['role']) : null,
            active: (bool) ($data['active'] ?? true),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->fullName,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email_address' => $this->emailAddress,
            'phone_number' => $this->phoneNumber,
            'role' => $this->role?->value,
            'active' => $this->active,
        ];
    }
}
