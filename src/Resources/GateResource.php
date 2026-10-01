<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Resources;

use DateTimeInterface;
use EventIO\ApiClient\Models\Gate;
use EventIO\ApiClient\Models\GateOccupancy;
use EventIO\ApiClient\Models\GatePassage;
use EventIO\ApiClient\Models\GatePresence;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Support\QueryBuilder;

/**
 * Gates are addressed by numeric id or by gate_key.
 */
final readonly class GateResource
{
    public function __construct(
        private HttpClient $http,
        private int $eventId,
    ) {}

    /**
     * @return QueryBuilder<Gate>
     */
    public function list(): QueryBuilder
    {
        return QueryBuilder::make($this->http, "event/{$this->eventId}/gates", Gate::class);
    }

    public function get(int|string $gateId): Gate
    {
        $response = $this->http->get("event/{$this->eventId}/gates/{$gateId}");

        return Gate::fromArray($response['data']);
    }

    public function occupancy(int|string $gateId): GateOccupancy
    {
        $response = $this->http->get("event/{$this->eventId}/gates/{$gateId}/occupancy");

        return GateOccupancy::fromArray($response);
    }

    /**
     * @return QueryBuilder<GatePresence>
     */
    public function roster(int|string $gateId): QueryBuilder
    {
        return QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/gates/{$gateId}/roster",
            GatePresence::class,
        );
    }

    /**
     * @return QueryBuilder<GatePassage>
     */
    public function passages(
        int|string $gateId,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
        ?int $perPage = null,
    ): QueryBuilder {
        $builder = QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/gates/{$gateId}/passages",
            GatePassage::class,
        );

        if ($from !== null) {
            $builder = $builder->param('from', $from->format(DateTimeInterface::ATOM));
        }
        if ($to !== null) {
            $builder = $builder->param('to', $to->format(DateTimeInterface::ATOM));
        }
        if ($perPage !== null) {
            $builder = $builder->param('per_page', $perPage);
        }

        return $builder;
    }

    /**
     * @return QueryBuilder<GatePassage>
     */
    public function participantPassages(int $participantId, ?int $perPage = null): QueryBuilder
    {
        $builder = QueryBuilder::make(
            $this->http,
            "event/{$this->eventId}/gates/participants/{$participantId}/passages",
            GatePassage::class,
        );

        if ($perPage !== null) {
            $builder = $builder->param('per_page', $perPage);
        }

        return $builder;
    }
}
