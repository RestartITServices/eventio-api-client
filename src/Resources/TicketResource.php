<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Ticket;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class TicketResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Ticket>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/tickets", Ticket::class);
    }

    public function get(int $ticketId): Ticket
    {
        $response = $this->http->get("event/{$this->eventId}/tickets/{$ticketId}");

        return Ticket::fromArray($response['data']);
    }
}
