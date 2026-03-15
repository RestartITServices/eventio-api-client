<?php

declare(strict_types=1);

use EventIO\ApiClient\Enums\BookingStatus;
use EventIO\ApiClient\Enums\NotificationStatus;
use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Enums\ParticipantType;
use EventIO\ApiClient\Enums\PermissionLevel;

test('BookingStatus enum has correct values', function () {
    expect(BookingStatus::Provisional->value)->toBe('provisional');
    expect(BookingStatus::Confirmed->value)->toBe('confirmed');
    expect(BookingStatus::WaitingList->value)->toBe('waiting_list');
    expect(BookingStatus::from('confirmed'))->toBe(BookingStatus::Confirmed);
});

test('NotificationType enum has correct values', function () {
    expect(NotificationType::InApp->value)->toBe('in_app');
    expect(NotificationType::Incident->value)->toBe('incident');
});

test('NotificationStatus enum has correct values', function () {
    expect(NotificationStatus::Scheduled->value)->toBe('scheduled');
    expect(NotificationStatus::Ready->value)->toBe('ready');
    expect(NotificationStatus::Sending->value)->toBe('sending');
    expect(NotificationStatus::Sent->value)->toBe('sent');
});

test('ParticipantType enum has correct values', function () {
    expect(ParticipantType::Participant->value)->toBe('participant');
    expect(ParticipantType::Leader->value)->toBe('leader');
    expect(ParticipantType::Attendee->value)->toBe('attendee');
    expect(ParticipantType::Staff->value)->toBe('staff');
    expect(ParticipantType::Contractor->value)->toBe('contractor');
    expect(ParticipantType::Visitor->value)->toBe('visitor');
});

test('PermissionLevel enum has correct values', function () {
    expect(PermissionLevel::Read->value)->toBe('read');
    expect(PermissionLevel::Write->value)->toBe('write');
    expect(PermissionLevel::Admin->value)->toBe('admin');
});
