<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum GroupUserRole: string
{
    case Owner = 'owner';
    case Member = 'member';
}
