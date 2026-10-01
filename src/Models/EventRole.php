<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class EventRole implements \JsonSerializable
{
    /**
     * @param list<EventRolePermission>|null $permissions
     * @param list<EventUser>|null $eventUsers
     */
    public function __construct(
        public int $id,
        public string $title,
        public ?Event $event = null,
        public ?array $permissions = null,
        public ?array $eventUsers = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $eventUsers = $data['eventUsers'] ?? $data['event_users'] ?? null;

        return new self(
            id: $data['id'],
            title: $data['title'],
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
            permissions: isset($data['permissions']) ? self::permissions($data['permissions'], $data['id']) : null,
            eventUsers: isset($eventUsers) ? array_values(array_map(EventUser::fromArray(...), $eventUsers)) : null,
        );
    }

    /**
     * The API returns permissions as an area => level map.
     *
     * @param array<int|string, mixed> $permissions
     * @return list<EventRolePermission>
     */
    private static function permissions(array $permissions, int $roleId): array
    {
        $result = [];

        foreach ($permissions as $area => $permission) {
            if ($permission === null) {
                continue;
            }

            $result[] = EventRolePermission::fromArray(is_array($permission) ? $permission : [
                'event_role_id' => $roleId,
                'area' => (string) $area,
                'permission' => $permission,
            ]);
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'event' => $this->event,
            'permissions' => $this->permissions,
            'event_users' => $this->eventUsers,
        ];
    }
}
