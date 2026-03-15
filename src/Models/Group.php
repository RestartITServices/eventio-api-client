<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class Group
{
    /**
     * @param list<Booking>|null $bookings
     */
    public function __construct(
        public int $id,
        public int $eventId,
        public string $name,
        public ?string $association = null,
        public ?Customer $customer = null,
        public ?array $bookings = null,
        public ?Event $event = null,
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
            customer: isset($data['customer']) ? Customer::fromArray($data['customer']) : null,
            bookings: isset($data['bookings']) ? array_values(array_map(Booking::fromArray(...), $data['bookings'])) : null,
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
        );
    }
}
