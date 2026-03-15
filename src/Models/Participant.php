<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;
use EventIO\ApiClient\Enums\ParticipantType;

final readonly class Participant
{
    public function __construct(
        public int $id,
        public ?string $wristbandId,
        public ParticipantType $participantType,
        public string $fullName,
        public ?string $email,
        public ?string $phoneNumber,
        public ?string $association,
        public bool $offSite,
        public ?DateTimeImmutable $checkedInAt,
        public ?Group $group = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            wristbandId: $data['wristband_id'] ?? null,
            participantType: ParticipantType::from($data['participant_type']),
            fullName: $data['full_name'],
            email: $data['email'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            association: $data['association'] ?? null,
            offSite: $data['off_site'],
            checkedInAt: isset($data['checked_in_at']) ? new DateTimeImmutable($data['checked_in_at']) : null,
            group: isset($data['group']) ? Group::fromArray($data['group']) : null,
        );
    }
}
