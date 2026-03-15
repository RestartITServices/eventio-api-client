<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Event;
use EventIO\ApiClient\Models\EventStats;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class EventResource
{
    public function __construct(
        private HttpClient $http,
        private ?int $eventId = null,
    ) {}

    /**
     * @return QueryBuilder<Event>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, 'events', Event::class);
    }

    public function get(?int $eventId = null): Event
    {
        $id = $eventId ?? $this->eventId;
        $response = $this->http->get("event/{$id}");

        return Event::fromArray($response['data']);
    }

    public function stats(
        ?int $eventId = null,
        ?int $ticketId = null,
        ?string $ticket = null,
        ?string $participantType = null,
        bool $noCache = false,
    ): EventStats {
        $id = $eventId ?? $this->eventId;
        $query = [];

        if ($ticketId !== null) {
            $query['ticketId'] = $ticketId;
        }
        if ($ticket !== null) {
            $query['ticket'] = $ticket;
        }
        if ($participantType !== null) {
            $query['participant_type'] = $participantType;
        }
        if ($noCache) {
            $query['no-cache'] = '1';
        }

        $response = $this->http->get("event/{$id}/stats", $query);

        return EventStats::fromArray($response);
    }

    public function tickets(?int $eventId = null): TicketResource
    {
        return new TicketResource($this->http, $this->resolveEventId($eventId));
    }

    public function bookings(?int $eventId = null): BookingResource
    {
        return new BookingResource($this->http, $this->resolveEventId($eventId));
    }

    public function groups(?int $eventId = null): GroupResource
    {
        return new GroupResource($this->http, $this->resolveEventId($eventId));
    }

    public function customers(?int $eventId = null): CustomerResource
    {
        return new CustomerResource($this->http, $this->resolveEventId($eventId));
    }

    public function roles(?int $eventId = null): EventRoleResource
    {
        return new EventRoleResource($this->http, $this->resolveEventId($eventId));
    }

    public function users(?int $eventId = null): EventUserResource
    {
        return new EventUserResource($this->http, $this->resolveEventId($eventId));
    }

    public function notifications(?int $eventId = null): NotificationResource
    {
        return new NotificationResource($this->http, $this->resolveEventId($eventId));
    }

    private function resolveEventId(?int $eventId): int
    {
        return $eventId ?? $this->eventId ?? throw new \InvalidArgumentException('An eventId is required.');
    }
}
