<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;

final readonly class GatePresence implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $gateId,
        public int $eventId,
        public int $participantId,
        public ?string $occupancyKey,
        public ?DateTimeImmutable $enteredAt,
        public ?int $entryPassageId,
        public ?Participant $participant = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            gateId: $data['gate_id'],
            eventId: $data['event_id'],
            participantId: $data['participant_id'],
            occupancyKey: $data['occupancy_key'] ?? null,
            enteredAt: isset($data['entered_at']) ? new DateTimeImmutable($data['entered_at']) : null,
            entryPassageId: $data['entry_passage_id'] ?? null,
            participant: isset($data['participant']) ? Participant::fromArray($data['participant']) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'gate_id' => $this->gateId,
            'event_id' => $this->eventId,
            'participant_id' => $this->participantId,
            'occupancy_key' => $this->occupancyKey,
            'entered_at' => $this->enteredAt?->format(\DateTimeInterface::ATOM),
            'entry_passage_id' => $this->entryPassageId,
            'participant' => $this->participant,
        ];
    }
}
