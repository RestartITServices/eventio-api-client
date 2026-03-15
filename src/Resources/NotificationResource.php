<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Notification;
use EventIO\ApiClient\Requests\CreateNotificationRequest;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class NotificationResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Notification>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/notifications",
            Notification::class,
        );
    }

    public function create(CreateNotificationRequest $request): Notification
    {
        $response = $this->http->post(
            "event/{$this->eventId}/notifications",
            $request->toArray(),
        );

        return Notification::fromArray($response['data']);
    }
}
