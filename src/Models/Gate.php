<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;
use EventIO\ApiClient\Enums\GateDirection;
use EventIO\ApiClient\Enums\GateMode;

final readonly class Gate implements \JsonSerializable
{
    /**
     * @param array<string, mixed>|null $activity
     */
    public function __construct(
        public int $id,
        public ?string $gateKey,
        public int $eventId,
        public ?int $activityId,
        public string $name,
        public ?string $description,
        public ?GateMode $mode,
        public ?string $modeLabel,
        public ?GateDirection $direction,
        public ?string $gateGroup,
        public ?int $capacity,
        public int $occupancy,
        public bool $allowRepeat,
        public ?int $repeatCooldownSeconds,
        public bool $requireCheckedIn,
        public bool $setsOffSite,
        public ?DateTimeImmutable $opensAt,
        public ?DateTimeImmutable $closesAt,
        public ?string $location,
        public ?float $latitude,
        public ?float $longitude,
        public bool $enabled,
        public bool $isOpen,
        public ?array $activity = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            gateKey: $data['gate_key'] ?? null,
            eventId: $data['event_id'],
            activityId: $data['activity_id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            mode: isset($data['mode']) ? GateMode::from($data['mode']) : null,
            modeLabel: $data['mode_label'] ?? null,
            direction: isset($data['direction']) ? GateDirection::from($data['direction']) : null,
            gateGroup: $data['gate_group'] ?? null,
            capacity: isset($data['capacity']) ? (int) $data['capacity'] : null,
            occupancy: (int) ($data['occupancy'] ?? 0),
            allowRepeat: (bool) ($data['allow_repeat'] ?? false),
            repeatCooldownSeconds: isset($data['repeat_cooldown_seconds']) ? (int) $data['repeat_cooldown_seconds'] : null,
            requireCheckedIn: (bool) ($data['require_checked_in'] ?? false),
            setsOffSite: (bool) ($data['sets_off_site'] ?? false),
            opensAt: isset($data['opens_at']) ? new DateTimeImmutable($data['opens_at']) : null,
            closesAt: isset($data['closes_at']) ? new DateTimeImmutable($data['closes_at']) : null,
            location: $data['location'] ?? null,
            latitude: isset($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: isset($data['longitude']) ? (float) $data['longitude'] : null,
            enabled: (bool) ($data['enabled'] ?? false),
            isOpen: (bool) ($data['is_open'] ?? false),
            activity: $data['activity'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'gate_key' => $this->gateKey,
            'event_id' => $this->eventId,
            'activity_id' => $this->activityId,
            'name' => $this->name,
            'description' => $this->description,
            'mode' => $this->mode?->value,
            'mode_label' => $this->modeLabel,
            'direction' => $this->direction?->value,
            'gate_group' => $this->gateGroup,
            'capacity' => $this->capacity,
            'occupancy' => $this->occupancy,
            'allow_repeat' => $this->allowRepeat,
            'repeat_cooldown_seconds' => $this->repeatCooldownSeconds,
            'require_checked_in' => $this->requireCheckedIn,
            'sets_off_site' => $this->setsOffSite,
            'opens_at' => $this->opensAt?->format(\DateTimeInterface::ATOM),
            'closes_at' => $this->closesAt?->format(\DateTimeInterface::ATOM),
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'enabled' => $this->enabled,
            'is_open' => $this->isOpen,
            'activity' => $this->activity,
        ];
    }
}
