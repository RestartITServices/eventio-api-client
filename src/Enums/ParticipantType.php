<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Enums;

enum ParticipantType: string
{
    case Participant = 'participant';
    case Leader = 'leader';
    case Attendee = 'attendee';
    case Staff = 'staff';
    case Contractor = 'contractor';
    case Visitor = 'visitor';
}
