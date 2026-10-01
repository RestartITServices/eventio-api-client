<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;

final readonly class Group implements \JsonSerializable
{
    /**
     * @param list<GroupUser>|null $users
     * @param list<Booking>|null $bookings
     */
    public function __construct(
        public int $id,
        public int $eventId,
        public string $name,
        public ?string $association = null,
        public ?array $users = null,
        public ?array $bookings = null,
        public ?Event $event = null,
        public ?DateTimeImmutable $checkedInAt = null,
        public ?string $postCode = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            eventId: $data['event_id'],
            name: $data['name'],
            association: $data['association'] ?? null,
            users: isset($data['users']) ? array_values(array_map(GroupUser::fromArray(...), $data['users'])) : null,
            bookings: isset($data['bookings']) ? array_values(array_map(Booking::fromArray(...), $data['bookings'])) : null,
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
            checkedInAt: isset($data['checked_in_at']) ? new DateTimeImmutable($data['checked_in_at']) : null,
            postCode: $data['post_code'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->eventId,
            'name' => $this->name,
            'association' => $this->association,
            'checked_in_at' => $this->checkedInAt?->format(\DateTimeInterface::ATOM),
            'post_code' => $this->postCode,
            'users' => $this->users,
            'bookings' => $this->bookings,
            'event' => $this->event,
        ];
    }
}
