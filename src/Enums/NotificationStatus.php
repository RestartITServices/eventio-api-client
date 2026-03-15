<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum NotificationStatus: string
{
    case Scheduled = 'scheduled';
    case Ready = 'ready';
    case Sending = 'sending';
    case Sent = 'sent';
}
