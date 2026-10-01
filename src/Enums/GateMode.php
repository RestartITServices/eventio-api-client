<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum GateMode: string
{
    case Count = 'count';
    case Occupancy = 'occupancy';
    case Directional = 'directional';
}
