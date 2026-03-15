<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum NotificationType: string
{
    case InApp = 'in_app';
    case Incident = 'incident';
}
