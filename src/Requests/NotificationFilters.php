<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Requests;

use EventIO\ApiClient\Enums\ParticipantType;

final readonly class NotificationFilters
{
    /**
     * @param list<int>|null $participantIds
     * @param list<ParticipantType>|null $participantType
     * @param list<int>|null $ticketId
     * @param list<string>|null $refIndex
     * @param list<int>|null $groupId
     * @param list<int>|null $teamId
     * @param list<int>|null $jobId
     */
    public function __construct(
        public ?array $participantIds = null,
        public ?array $participantType = null,
        public ?array $ticketId = null,
        public ?bool $checkedIn = null,
        public ?bool $over18 = null,
        public ?array $refIndex = null,
        public ?array $groupId = null,
        public ?array $teamId = null,
        public ?array $jobId = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [];

        if ($this->participantIds !== null) {
            $data['participant_ids'] = $this->participantIds;
        }

        if ($this->participantType !== null) {
            $data['participant_type'] = array_map(
                fn (ParticipantType $type) => $type->value,
                $this->participantType,
            );
        }

        if ($this->ticketId !== null) {
            $data['ticket_id'] = $this->ticketId;
        }

        if ($this->checkedIn !== null) {
            $data['checked_in'] = $this->checkedIn;
        }

        if ($this->over18 !== null) {
            $data['over_18'] = $this->over18;
        }

        if ($this->refIndex !== null) {
            $data['ref_index'] = $this->refIndex;
        }

        if ($this->groupId !== null) {
            $data['group_id'] = $this->groupId;
        }

        if ($this->teamId !== null) {
            $data['team_id'] = $this->teamId;
        }

        if ($this->jobId !== null) {
            $data['job_id'] = $this->jobId;
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            participantIds: $data['participant_ids'] ?? null,
            participantType: isset($data['participant_type'])
                ? array_values(array_map(ParticipantType::from(...), $data['participant_type']))
                : null,
            ticketId: $data['ticket_id'] ?? null,
            checkedIn: $data['checked_in'] ?? null,
            over18: $data['over_18'] ?? null,
            refIndex: $data['ref_index'] ?? null,
            groupId: $data['group_id'] ?? null,
            teamId: $data['team_id'] ?? null,
            jobId: $data['job_id'] ?? null,
        );
    }
}
