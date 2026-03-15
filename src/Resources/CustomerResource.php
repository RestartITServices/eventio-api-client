<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Customer;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class CustomerResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Customer>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/customers", Customer::class);
    }

    public function get(int $customerId): Customer
    {
        $response = $this->http->get("event/{$this->eventId}/customers/{$customerId}");

        return Customer::fromArray($response['data']);
    }
}
