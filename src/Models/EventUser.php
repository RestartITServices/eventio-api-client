<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class EventUser
{
    public function __construct(
        public int $id,
        public int $userId,
        public int $eventId,
        public bool $active,
        public ?User $user = null,
        public ?Event $event = null,
        public ?EventRole $role = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            userId: $data['user_id'],
            eventId: $data['event_id'],
            active: $data['active'],
            user: isset($data['user']) ? User::fromArray($data['user']) : null,
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
            role: isset($data['role']) ? EventRole::fromArray($data['role']) : null,
        );
    }
}
