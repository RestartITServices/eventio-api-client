<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class GateOccupancy implements \JsonSerializable
{
    public function __construct(
        public int $gateId,
        public ?string $occupancyKey,
        public bool $tracksOccupancy,
        public int $occupancy,
        public ?int $capacity,
        public ?int $remaining,
        public bool $isOpen,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            gateId: $data['gate_id'],
            occupancyKey: $data['occupancy_key'] ?? null,
            tracksOccupancy: (bool) $data['tracks_occupancy'],
            occupancy: (int) $data['occupancy'],
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null,
            remaining: isset($data['remaining']) ? (int) $data['remaining'] : null,
            isOpen: (bool) $data['is_open'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'gate_id' => $this->gateId,
            'occupancy_key' => $this->occupancyKey,
            'tracks_occupancy' => $this->tracksOccupancy,
            'occupancy' => $this->occupancy,
            'capacity' => $this->capacity,
            'remaining' => $this->remaining,
            'is_open' => $this->isOpen,
        ];
    }
}
