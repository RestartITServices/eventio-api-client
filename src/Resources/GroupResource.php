<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Booking;
use EventIO\ApiClient\Models\Group;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class GroupResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Group>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/groups", Group::class);
    }

    public function get(int $groupId): Group
    {
        $response = $this->http->get("event/{$this->eventId}/groups/{$groupId}");

        return Group::fromArray($response['data']);
    }

    /**
     * @return QueryBuilder<Booking>
     */
    public function bookings(int $groupId, bool $includeCancelled = false): QueryBuilder
    {
        $builder = QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/groups/{$groupId}/bookings",
            Booking::class,
        );

        if ($includeCancelled) {
            $builder = $builder->param('include_cancelled', '1');
        }

        return $builder;
    }
}
