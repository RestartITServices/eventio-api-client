<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum BookingStatus: string
{
    case Provisional = 'provisional';
    case Confirmed = 'confirmed';
    case WaitingList = 'waiting_list';
}
