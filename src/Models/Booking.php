<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;
use EventIO\ApiClient\Enums\BookingStatus;

final readonly class Booking implements \JsonSerializable
{
    /**
     * @param list<BookingTicket>|null $tickets
     */
    public function __construct(
        public int $id,
        public int $eventId,
        public string $bookingType,
        public string $bookingReference,
        public BookingStatus $status,
        public DateTimeImmutable $createdAt,
        public ?array $tickets = null,
        public ?Group $group = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            eventId: $data['event_id'],
            bookingType: $data['booking_type'],
            bookingReference: $data['booking_reference'],
            status: BookingStatus::from($data['status']),
            createdAt: new DateTimeImmutable($data['created_at']),
            tickets: isset($data['tickets']) ? array_values(array_map(BookingTicket::fromArray(...), $data['tickets'])) : null,
            group: isset($data['group']) ? Group::fromArray($data['group']) : null,
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
            'booking_type' => $this->bookingType,
            'booking_reference' => $this->bookingReference,
            'status' => $this->status->value,
            'created_at' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'tickets' => $this->tickets,
            'group' => $this->group,
        ];
    }
}
