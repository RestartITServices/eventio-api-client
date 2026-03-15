<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;

final readonly class EventStats implements \JsonSerializable
{
    /**
     * @param list<EventStatItem> $stats
     */
    public function __construct(
        public int $eventId,
        public string $eventName,
        public array $stats,
        public int $totalConfirmed,
        public int $totalProvisional,
        public DateTimeImmutable $generatedAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            eventId: $data['event_id'],
            eventName: $data['event_name'],
            stats: array_values(array_map(EventStatItem::fromArray(...), $data['stats'])),
            totalConfirmed: $data['total_confirmed'],
            totalProvisional: $data['total_provisional'],
            generatedAt: new DateTimeImmutable($data['generated_at']),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_name' => $this->eventName,
            'stats' => $this->stats,
            'total_confirmed' => $this->totalConfirmed,
            'total_provisional' => $this->totalProvisional,
            'generated_at' => $this->generatedAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
