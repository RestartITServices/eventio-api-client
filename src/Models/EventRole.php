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
        return new self(
            id: $data['id'],
            title: $data['title'],
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
            permissions: isset($data['permissions']) ? array_values(array_map(EventRolePermission::fromArray(...), $data['permissions'])) : null,
            eventUsers: isset($data['event_users']) ? array_values(array_map(EventUser::fromArray(...), $data['event_users'])) : null,
        );
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
