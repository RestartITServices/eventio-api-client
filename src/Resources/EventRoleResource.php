<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\EventRole;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class EventRoleResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<EventRole>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/roles", EventRole::class);
    }

    public function get(int $roleId): EventRole
    {
        $response = $this->http->get("event/{$this->eventId}/roles/{$roleId}");

        return EventRole::fromArray($response['data']);
    }
}
