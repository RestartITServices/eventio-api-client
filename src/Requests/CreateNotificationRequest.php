<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Requests;

use DateTimeInterface;
use EventIO\ApiClient\Enums\NotificationType;

final readonly class CreateNotificationRequest
{
    public function __construct(
        public string $title,
        public string $content,
        public NotificationType $type,
        public ?string $body = null,
        public ?DateTimeInterface $scheduledAt = null,
        public ?NotificationFilters $filters = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type->value,
        ];

        if ($this->body !== null) {
            $data['body'] = $this->body;
        }

        if ($this->scheduledAt !== null) {
            $data['scheduled_at'] = $this->scheduledAt->format(DateTimeInterface::ATOM);
        }

        if ($this->filters !== null) {
            $data['filters'] = $this->filters->toArray();
        }

        return $data;
    }
}
