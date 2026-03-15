<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use EventIO\ApiClient\Enums\PermissionLevel;

final readonly class EventRolePermission
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
}
