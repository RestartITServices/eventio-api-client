<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class BookingTicket
{
    public function __construct(
        public int $id,
        public int $bookingId,
        public int $ticketId,
        public ?string $ticketTitle = null,
        public ?string $participantType = null,
        public int $qty = 0,
        public string $price = '0.00',
        public ?Booking $booking = null,
        public ?Ticket $ticket = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            bookingId: $data['booking_id'],
            ticketId: $data['ticket_id'],
            ticketTitle: $data['ticket_title'] ?? null,
            participantType: $data['participant_type'] ?? null,
            qty: $data['qty'] ?? 0,
            price: $data['price'] ?? '0.00',
            booking: isset($data['booking']) ? Booking::fromArray($data['booking']) : null,
            ticket: isset($data['ticket']) ? Ticket::fromArray($data['ticket']) : null,
        );
    }
}
