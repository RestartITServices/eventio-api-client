<?php

declare(strict_types=1);

use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Enums\ParticipantType;
use EventIO\ApiClient\Requests\CreateNotificationRequest;
use EventIO\ApiClient\Requests\NotificationFilters;

test('NotificationFilters serializes to array', function () {
    $filters = new NotificationFilters(
        participantType: [ParticipantType::Participant, ParticipantType::Leader],
        checkedIn: true,
        ticketId: [1, 2],
    );

    $array = $filters->toArray();

    expect($array)->toBe([
        'participant_type' => ['participant', 'leader'],
        'ticket_id' => [1, 2],
        'checked_in' => true,
    ]);
});

test('NotificationFilters omits null fields', function () {
    $filters = new NotificationFilters(checkedIn: false);

    expect($filters->toArray())->toBe(['checked_in' => false]);
});

test('NotificationFilters deserializes from array', function () {
    $filters = NotificationFilters::fromArray([
        'checked_in' => true,
        'participant_type' => ['participant', 'leader'],
        'group_id' => [1, 2],
    ]);

    expect($filters->checkedIn)->toBeTrue();
    expect($filters->participantType)->toHaveCount(2);
    expect($filters->participantType[0])->toBe(ParticipantType::Participant);
    expect($filters->groupId)->toBe([1, 2]);
    expect($filters->ticketId)->toBeNull();
});

test('NotificationFilters roundtrip serialization', function () {
    $original = new NotificationFilters(
        participantIds: [10, 20],
        participantType: [ParticipantType::Staff],
        over18: true,
        refIndex: ['WB-001', 'WB-002'],
    );

    $deserialized = NotificationFilters::fromArray($original->toArray());

    expect($deserialized->participantIds)->toBe([10, 20]);
    expect($deserialized->participantType)->toHaveCount(1);
    expect($deserialized->participantType[0])->toBe(ParticipantType::Staff);
    expect($deserialized->over18)->toBeTrue();
    expect($deserialized->refIndex)->toBe(['WB-001', 'WB-002']);
    expect($deserialized->checkedIn)->toBeNull();
});

test('CreateNotificationRequest serializes to array', function () {
    $request = new CreateNotificationRequest(
        title: 'Weather Alert',
        content: 'Heavy rain expected.',
        type: NotificationType::Incident,
        filters: new NotificationFilters(
            checkedIn: true,
            participantType: [ParticipantType::Participant, ParticipantType::Leader],
        ),
    );

    $array = $request->toArray();

    expect($array)->toBe([
        'title' => 'Weather Alert',
        'content' => 'Heavy rain expected.',
        'type' => 'incident',
        'filters' => [
            'participant_type' => ['participant', 'leader'],
            'checked_in' => true,
        ],
    ]);
});

test('CreateNotificationRequest omits optional null fields', function () {
    $request = new CreateNotificationRequest(
        title: 'Hello',
        content: 'World',
        type: NotificationType::InApp,
    );

    $array = $request->toArray();

    expect($array)->toBe([
        'title' => 'Hello',
        'content' => 'World',
        'type' => 'in_app',
    ]);
    expect($array)->not->toHaveKey('body');
    expect($array)->not->toHaveKey('scheduled_at');
    expect($array)->not->toHaveKey('filters');
});

test('CreateNotificationRequest with scheduled_at', function () {
    $request = new CreateNotificationRequest(
        title: 'Scheduled',
        content: 'Test',
        type: NotificationType::InApp,
        scheduledAt: new DateTimeImmutable('2026-03-15T10:00:00+00:00'),
    );

    $array = $request->toArray();

    expect($array['scheduled_at'])->toBe('2026-03-15T10:00:00+00:00');
});
