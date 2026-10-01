<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;
use EventIO\ApiClient\Enums\GateCaptureMethod;
use EventIO\ApiClient\Enums\GateDirection;

final readonly class GatePassage implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $gateId,
        public int $eventId,
        public int $participantId,
        public ?GateDirection $direction,
        public ?DateTimeImmutable $passedAt,
        public ?GateCaptureMethod $captureMethod = null,
        public ?int $recordedByUserId = null,
        public ?int $recordedByParticipantId = null,
        public ?string $deviceId = null,
        public ?float $latitude = null,
        public ?float $longitude = null,
        public ?string $overrideReason = null,
        public ?string $notes = null,
        public ?Gate $gate = null,
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
            direction: isset($data['direction']) ? GateDirection::from($data['direction']) : null,
            passedAt: isset($data['passed_at']) ? new DateTimeImmutable($data['passed_at']) : null,
            captureMethod: isset($data['capture_method']) ? GateCaptureMethod::from($data['capture_method']) : null,
            recordedByUserId: $data['recorded_by_user_id'] ?? null,
            recordedByParticipantId: $data['recorded_by_participant_id'] ?? null,
            deviceId: $data['device_id'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            overrideReason: $data['override_reason'] ?? null,
            notes: $data['notes'] ?? null,
            gate: isset($data['gate']) ? Gate::fromArray($data['gate']) : null,
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
            'direction' => $this->direction?->value,
            'passed_at' => $this->passedAt?->format(\DateTimeInterface::ATOM),
            'capture_method' => $this->captureMethod?->value,
            'recorded_by_user_id' => $this->recordedByUserId,
            'recorded_by_participant_id' => $this->recordedByParticipantId,
            'device_id' => $this->deviceId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'override_reason' => $this->overrideReason,
            'notes' => $this->notes,
            'gate' => $this->gate,
            'participant' => $this->participant,
        ];
    }
}
