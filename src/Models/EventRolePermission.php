<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use EventIO\ApiClient\Enums\PermissionLevel;

final readonly class EventRolePermission implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $eventRoleId,
        public string $area,
        public PermissionLevel $permission,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            eventRoleId: $data['event_role_id'],
            area: $data['area'],
            permission: PermissionLevel::from($data['permission']),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'event_role_id' => $this->eventRoleId,
            'area' => $this->area,
            'permission' => $this->permission->value,
        ];
    }
}
