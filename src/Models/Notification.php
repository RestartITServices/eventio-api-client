<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Models;

use DateTimeImmutable;
use EventIO\ApiClient\Enums\NotificationStatus;
use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Requests\NotificationFilters;

final readonly class Notification
{
    public function __construct(
        public int $id,
        public int $eventId,
        public string $title,
        public string $content,
        public NotificationType $type,
        public NotificationStatus $status,
        public ?NotificationFilters $filters,
        public ?DateTimeImmutable $scheduledAt,
        public ?DateTimeImmutable $sentAt,
        public ?int $users,
        public ?int $devices,
        public DateTimeImmutable $createdAt,
        public ?string $selfLink = null,
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
            content: $data['content'],
            type: NotificationType::from($data['type']),
            status: NotificationStatus::from($data['status']),
            filters: isset($data['filters']) ? NotificationFilters::fromArray($data['filters']) : null,
            scheduledAt: isset($data['scheduled_at']) ? new DateTimeImmutable($data['scheduled_at']) : null,
            sentAt: isset($data['sent_at']) ? new DateTimeImmutable($data['sent_at']) : null,
            users: $data['users'] ?? null,
            devices: $data['devices'] ?? null,
            createdAt: new DateTimeImmutable($data['created_at']),
            selfLink: $data['links']['self'] ?? null,
        );
    }
}
