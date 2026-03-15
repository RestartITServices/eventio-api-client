<?php

declare(strict_types=1);

namespace EventIO\ApiClient;

use EventIO\ApiClient\Models\User;
use EventIO\ApiClient\Resources\EventResource;
use EventIO\ApiClient\Resources\UserResource;
use EventIO\ApiClient\Support\HttpClient;
use GuzzleHttp\Client as Guzzle;

final class Client
{
    public const string BASE_URL = 'https://api.eventio.uk/api/v2';

    private readonly HttpClient $http;

    public function __construct(
        string $token,
        string $baseUrl = self::BASE_URL,
        ?Guzzle $guzzle = null,
        ?string $tenant = null,
    ) {
        if ($baseUrl === self::BASE_URL && $tenant === null) {
            throw new \InvalidArgumentException('A tenant must be provided when using the default base URL.');
        }

        $this->http = new HttpClient($baseUrl, $token, $guzzle, $tenant);
    }

    public function user(?int $eventId = null): User
    {
        return (new UserResource($this->http))->get($eventId);
    }

    public function events(): EventResource
    {
        return new EventResource($this->http);
    }

    public function event(int $eventId): EventResource
    {
        return new EventResource($this->http, $eventId);
    }
}
