<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\User;
use EventIO\ApiClient\Support\HttpClient;

final readonly class UserResource
{
    public function __construct(
        private HttpClient $http,
    ) {}

    public function get(?int $eventId = null): User
    {
        $query = $eventId !== null ? ['eventId' => $eventId] : [];
        $response = $this->http->get('user', $query);

        $data = $response['data'];

        if (isset($response['event_role'])) {
            $data['event_role'] = $response['event_role'];
        }

        return User::fromArray($data);
    }
}
