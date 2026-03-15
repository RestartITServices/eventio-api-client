<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;

final readonly class Event implements \JsonSerializable
{
    /**
     * @param list<Ticket>|null $tickets
     * @param list<Booking>|null $bookings
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $name,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public ?array $tickets = null,
        public ?array $bookings = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            slug: $data['slug'],
            name: $data['name'],
            startDate: new DateTimeImmutable($data['start_date']),
            endDate: new DateTimeImmutable($data['end_date']),
            tickets: isset($data['tickets']) ? array_values(array_map(Ticket::fromArray(...), $data['tickets'])) : null,
            bookings: isset($data['bookings']) ? array_values(array_map(Booking::fromArray(...), $data['bookings'])) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'tickets' => $this->tickets,
            'bookings' => $this->bookings,
        ];
    }
}
