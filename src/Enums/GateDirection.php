<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum GateDirection: string
{
    case In = 'in';
    case Out = 'out';
}
