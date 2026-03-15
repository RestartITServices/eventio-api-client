<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\EventUser;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class EventUserResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<EventUser>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/users", EventUser::class);
    }

    public function get(int $eventUserId): EventUser
    {
        $response = $this->http->get("event/{$this->eventId}/users/{$eventUserId}");

        return EventUser::fromArray($response['data']);
    }
}
