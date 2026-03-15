<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\BookingTicket;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class BookingTicketResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
        private int $bookingId,
    ) {}

    /**
     * @return QueryBuilder<BookingTicket>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/bookings/{$this->bookingId}/tickets",
            BookingTicket::class,
        );
    }

    public function get(int $bookingTicketId): BookingTicket
    {
        $response = $this->http->get(
            "event/{$this->eventId}/bookings/{$this->bookingId}/tickets/{$bookingTicketId}",
        );

        return BookingTicket::fromArray($response['data']);
    }
}
