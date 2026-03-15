<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum PermissionLevel: string
{
    case Read = 'read';
    case Write = 'write';
    case Admin = 'admin';
}
