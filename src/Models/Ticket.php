<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

final readonly class Ticket implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public int $eventId,
        public string $title,
        public string $participantType,
        public string $price,
        public ?Event $event = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            eventId: $data['event_id'],
            title: $data['title'],
            participantType: $data['participant_type'],
            price: $data['price'],
            event: isset($data['event']) ? Event::fromArray($data['event']) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'event_id' => $this->eventId,
            'title' => $this->title,
            'participant_type' => $this->participantType,
            'price' => $this->price,
            'event' => $this->event,
        ];
    }
}
