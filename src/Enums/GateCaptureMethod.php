<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum GateCaptureMethod: string
{
    case Qr = 'qr';
    case Wristband = 'wristband';
    case Tag = 'tag';
    case Manual = 'manual';
}
