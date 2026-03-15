<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Booking;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class BookingResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Booking>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/bookings", Booking::class);
    }

    public function get(int $bookingId): Booking
    {
        $response = $this->http->get("event/{$this->eventId}/bookings/{$bookingId}");

        return Booking::fromArray($response['data']);
    }

    public function tickets(int $bookingId): BookingTicketResource
    {
        return new BookingTicketResource($this->http, $this->eventId, $bookingId);
    }
}
