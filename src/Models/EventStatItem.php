<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class EventStatItem implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $participantType,
        public string $title,
        public int $totalConfirmedTicketsSold,
        public int $totalProvisionalTicketsSold,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            participantType: $data['participant_type'],
            title: $data['title'],
            totalConfirmedTicketsSold: $data['total_confirmed_tickets_sold'],
            totalProvisionalTicketsSold: $data['total_provisional_tickets_sold'],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'participant_type' => $this->participantType,
            'title' => $this->title,
            'total_confirmed_tickets_sold' => $this->totalConfirmedTicketsSold,
            'total_provisional_tickets_sold' => $this->totalProvisionalTicketsSold,
        ];
    }
}
