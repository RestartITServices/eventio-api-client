<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use EventIO\ApiClient\Models\Participant;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

final readonly class ParticipantResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Participant>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/participants", Participant::class);
    }

    public function get(string $participantKey): Participant
    {
        $response = $this->http->get("event/{$this->eventId}/participants/{$participantKey}");

        return Participant::fromArray($response['data']);
    }

    public function getByWristband(string $wristbandId): Participant
    {
        $response = $this->http->get("event/{$this->eventId}/participants/wristband/{$wristbandId}");

        return Participant::fromArray($response['data']);
    }
}
