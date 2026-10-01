<?php

declare(strict_types=1);

use EventIO\ApiClient\Models\Booking;
use EventIO\ApiClient\Models\BookingTicket;
use EventIO\ApiClient\Models\Gate;
use EventIO\ApiClient\Models\GatePassage;
use EventIO\ApiClient\Models\GroupUser;
use EventIO\ApiClient\Models\Event;
use EventIO\ApiClient\Models\EventRole;
use EventIO\ApiClient\Models\EventRolePermission;
use EventIO\ApiClient\Models\EventStatItem;
use EventIO\ApiClient\Models\EventStats;
use EventIO\ApiClient\Models\EventUser;
use EventIO\ApiClient\Models\Group;
use EventIO\ApiClient\Models\Notification;
use EventIO\ApiClient\Models\Participant;
use EventIO\ApiClient\Models\Ticket;
use EventIO\ApiClient\Models\User;
use EventIO\ApiClient\Enums\BookingStatus;
use EventIO\ApiClient\Enums\GateCaptureMethod;
use EventIO\ApiClient\Enums\GateDirection;
use EventIO\ApiClient\Enums\GateMode;
use EventIO\ApiClient\Enums\GroupUserRole;
use EventIO\ApiClient\Enums\NotificationStatus;
use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Enums\ParticipantType;
use EventIO\ApiClient\Enums\PermissionLevel;

test('User::fromArray creates user model', function () {
    $user = User::fromArray([
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    expect($user->id)->toBe(1);
    expect($user->name)->toBe('John Doe');
    expect($user->email)->toBe('john@example.com');
    expect($user->eventRole)->toBeNull();
});

test('User::fromArray with event role', function () {
    $user = User::fromArray([
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'event_role' => [
            'id' => 1,
            'title' => 'Event Manager',
        ],
    ]);

    expect($user->eventRole)->not->toBeNull();
    expect($user->eventRole->title)->toBe('Event Manager');
});

test('Event::fromArray creates event model', function () {
    $event = Event::fromArray([
        'id' => 1,
        'slug' => 'conference-2026',
        'name' => 'Annual Conference 2026',
        'start_date' => '2026-06-15',
        'end_date' => '2026-06-17',
    ]);

    expect($event->id)->toBe(1);
    expect($event->slug)->toBe('conference-2026');
    expect($event->name)->toBe('Annual Conference 2026');
    expect($event->startDate->format('Y-m-d'))->toBe('2026-06-15');
    expect($event->endDate->format('Y-m-d'))->toBe('2026-06-17');
    expect($event->tickets)->toBeNull();
    expect($event->bookings)->toBeNull();
});

test('Event::fromArray with included tickets', function () {
    $event = Event::fromArray([
        'id' => 1,
        'slug' => 'conference-2026',
        'name' => 'Annual Conference 2026',
        'start_date' => '2026-06-15',
        'end_date' => '2026-06-17',
        'tickets' => [
            ['id' => 1, 'event_id' => 1, 'title' => 'Adult Ticket', 'participant_type' => 'adult', 'price' => '99.00'],
        ],
    ]);

    expect($event->tickets)->toHaveCount(1);
    expect($event->tickets[0]->title)->toBe('Adult Ticket');
});

test('Ticket::fromArray creates ticket model', function () {
    $ticket = Ticket::fromArray([
        'id' => 1,
        'event_id' => 1,
        'title' => 'Adult Ticket',
        'participant_type' => 'adult',
        'price' => '99.00',
    ]);

    expect($ticket->id)->toBe(1);
    expect($ticket->participantType)->toBe('adult');
    expect($ticket->price)->toBe('99.00');
});

test('Booking::fromArray creates booking model', function () {
    $booking = Booking::fromArray([
        'id' => 1,
        'event_id' => 1,
        'booking_type' => 'participant',
        'booking_reference' => 'EVT-ABC123',
        'status' => 'confirmed',
        'created_at' => '2026-01-10T14:30:00.000000Z',
    ]);

    expect($booking->id)->toBe(1);
    expect($booking->status)->toBe(BookingStatus::Confirmed);
    expect($booking->bookingReference)->toBe('EVT-ABC123');
    expect($booking->createdAt->format('Y-m-d'))->toBe('2026-01-10');
});

test('Booking::fromArray with included group and tickets', function () {
    $booking = Booking::fromArray([
        'id' => 1,
        'event_id' => 1,
        'booking_type' => 'participant',
        'booking_reference' => 'EVT-ABC123',
        'status' => 'confirmed',
        'created_at' => '2026-01-10T14:30:00.000000Z',
        'tickets' => [
            ['id' => 1, 'booking_id' => 1, 'ticket_id' => 1, 'qty' => 2, 'price' => '198.00'],
        ],
        'group' => [
            'id' => 1,
            'event_id' => 1,
            'name' => 'ACME Corp',
            'users' => [
                ['id' => 1, 'full_name' => 'Jane Smith', 'email_address' => 'jane@acme.com', 'role' => 'owner', 'active' => true],
            ],
        ],
    ]);

    expect($booking->tickets)->toHaveCount(1);
    expect($booking->tickets[0]->qty)->toBe(2);
    expect($booking->group)->not->toBeNull();
    expect($booking->group->name)->toBe('ACME Corp');
    expect($booking->group->users[0]->fullName)->toBe('Jane Smith');
});

test('BookingTicket::fromArray creates booking ticket model', function () {
    $bt = BookingTicket::fromArray([
        'id' => 1,
        'booking_id' => 1,
        'ticket_id' => 1,
        'ticket_title' => 'Adult Ticket',
        'participant_type' => 'participant',
        'qty' => 2,
        'price' => '198.00',
    ]);

    expect($bt->id)->toBe(1);
    expect($bt->ticketTitle)->toBe('Adult Ticket');
    expect($bt->participantType)->toBe('participant');
    expect($bt->qty)->toBe(2);
});

test('Group::fromArray creates group model', function () {
    $group = Group::fromArray([
        'id' => 1,
        'event_id' => 1,
        'name' => 'ACME Corporation',
        'association' => 'Corporate Partner',
    ]);

    expect($group->id)->toBe(1);
    expect($group->name)->toBe('ACME Corporation');
    expect($group->association)->toBe('Corporate Partner');
});

test('GroupUser::fromArray creates group user model', function () {
    $user = GroupUser::fromArray([
        'id' => 1,
        'full_name' => 'Jane Smith',
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'email_address' => 'jane@example.com',
        'phone_number' => null,
        'role' => 'owner',
        'active' => true,
    ]);

    expect($user->id)->toBe(1);
    expect($user->fullName)->toBe('Jane Smith');
    expect($user->emailAddress)->toBe('jane@example.com');
    expect($user->role)->toBe(GroupUserRole::Owner);
});

test('EventRole::fromArray with permissions keyed by area', function () {
    $role = EventRole::fromArray([
        'id' => 1,
        'title' => 'Event Manager',
        'permissions' => ['event' => 'admin', 'bookings' => 'write'],
        'eventUsers' => [
            ['id' => 1, 'user_id' => 5, 'event_id' => 1, 'active' => true],
        ],
    ]);

    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions[0]->area)->toBe('event');
    expect($role->permissions[0]->permission)->toBe(PermissionLevel::Admin);
    expect($role->permissions[1]->area)->toBe('bookings');
    expect($role->eventUsers)->toHaveCount(1);
});

test('Gate::fromArray creates gate model', function () {
    $gate = Gate::fromArray([
        'id' => 3,
        'gate_key' => 'main-entrance',
        'event_id' => 1,
        'activity_id' => null,
        'name' => 'Main Entrance',
        'description' => null,
        'mode' => 'occupancy',
        'mode_label' => 'Occupancy',
        'direction' => null,
        'gate_group' => 'site',
        'capacity' => 500,
        'occupancy' => 120,
        'allow_repeat' => true,
        'repeat_cooldown_seconds' => null,
        'require_checked_in' => true,
        'sets_off_site' => false,
        'opens_at' => '2026-06-15T08:00:00.000000Z',
        'closes_at' => null,
        'location' => null,
        'latitude' => '51.5007',
        'longitude' => '-0.1246',
        'enabled' => true,
        'is_open' => true,
    ]);

    expect($gate->gateKey)->toBe('main-entrance');
    expect($gate->mode)->toBe(GateMode::Occupancy);
    expect($gate->direction)->toBeNull();
    expect($gate->capacity)->toBe(500);
    expect($gate->latitude)->toBe(51.5007);
    expect($gate->opensAt?->format('Y-m-d'))->toBe('2026-06-15');
});

test('GatePassage::fromArray with included participant', function () {
    $passage = GatePassage::fromArray([
        'id' => 9,
        'gate_id' => 3,
        'event_id' => 1,
        'participant_id' => 7,
        'direction' => 'in',
        'passed_at' => '2026-06-15T09:00:00.000000Z',
        'capture_method' => 'qr',
        'participant' => [
            'id' => 7,
            'ref_index' => 1001,
            'participant_type' => 'participant',
            'full_name' => 'John Doe',
            'off_site' => false,
        ],
    ]);

    expect($passage->direction)->toBe(GateDirection::In);
    expect($passage->captureMethod)->toBe(GateCaptureMethod::Qr);
    expect($passage->participant->fullName)->toBe('John Doe');
});

test('EventRole::fromArray with permissions', function () {
    $role = EventRole::fromArray([
        'id' => 1,
        'title' => 'Event Manager',
        'permissions' => [
            ['id' => 1, 'event_role_id' => 1, 'area' => 'event', 'permission' => 'admin'],
            ['id' => 2, 'event_role_id' => 1, 'area' => 'bookings', 'permission' => 'write'],
        ],
    ]);

    expect($role->title)->toBe('Event Manager');
    expect($role->permissions)->toHaveCount(2);
    expect($role->permissions[0]->area)->toBe('event');
    expect($role->permissions[0]->permission)->toBe(PermissionLevel::Admin);
    expect($role->permissions[1]->area)->toBe('bookings');
});

test('EventRolePermission::fromArray creates permission model', function () {
    $perm = EventRolePermission::fromArray([
        'id' => 1,
        'event_role_id' => 1,
        'area' => 'customers',
        'permission' => 'read',
    ]);

    expect($perm->area)->toBe('customers');
    expect($perm->permission)->toBe(PermissionLevel::Read);
});

test('EventUser::fromArray creates event user model', function () {
    $eu = EventUser::fromArray([
        'id' => 1,
        'user_id' => 5,
        'event_id' => 1,
        'active' => true,
    ]);

    expect($eu->userId)->toBe(5);
    expect($eu->active)->toBeTrue();
    expect($eu->user)->toBeNull();
});

test('EventUser::fromArray with included user and role', function () {
    $eu = EventUser::fromArray([
        'id' => 1,
        'user_id' => 5,
        'event_id' => 1,
        'active' => true,
        'user' => ['id' => 5, 'name' => 'John Doe', 'email' => 'john@example.com'],
        'role' => ['id' => 1, 'title' => 'Event Manager'],
    ]);

    expect($eu->user->name)->toBe('John Doe');
    expect($eu->role->title)->toBe('Event Manager');
});

test('EventStats::fromArray creates stats model', function () {
    $stats = EventStats::fromArray([
        'event_id' => 1,
        'event_name' => 'Annual Conference 2026',
        'stats' => [
            [
                'id' => 1,
                'participant_type' => 'adult',
                'title' => 'Adult Ticket',
                'total_confirmed_tickets_sold' => 150,
                'total_provisional_tickets_sold' => 25,
            ],
        ],
        'total_confirmed' => 150,
        'total_provisional' => 175,
        'generated_at' => '2026-01-15T10:30:00+00:00',
    ]);

    expect($stats->eventId)->toBe(1);
    expect($stats->totalConfirmed)->toBe(150);
    expect($stats->stats)->toHaveCount(1);
    expect($stats->stats[0]->title)->toBe('Adult Ticket');
    expect($stats->stats[0]->totalConfirmedTicketsSold)->toBe(150);
});

test('Notification::fromArray creates notification model', function () {
    $notif = Notification::fromArray([
        'id' => 1,
        'event_id' => 1,
        'title' => 'Welcome!',
        'content' => 'Check in at registration desk.',
        'type' => 'in_app',
        'status' => 'sent',
        'filters' => null,
        'scheduled_at' => '2026-03-13T10:00:00.000000Z',
        'sent_at' => '2026-03-13T10:00:05.000000Z',
        'users' => 150,
        'devices' => 132,
        'created_at' => '2026-03-13T09:45:00.000000Z',
        'links' => ['self' => '/api/v2/event/1/notifications/1'],
    ]);

    expect($notif->type)->toBe(NotificationType::InApp);
    expect($notif->status)->toBe(NotificationStatus::Sent);
    expect($notif->users)->toBe(150);
    expect($notif->selfLink)->toBe('/api/v2/event/1/notifications/1');
    expect($notif->filters)->toBeNull();
});

test('Notification::fromArray with typed filters', function () {
    $notif = Notification::fromArray([
        'id' => 42,
        'event_id' => 1,
        'title' => 'Weather Alert',
        'content' => 'Heavy rain expected.',
        'type' => 'incident',
        'status' => 'scheduled',
        'filters' => [
            'checked_in' => true,
            'participant_type' => ['participant', 'leader', 'attendee'],
        ],
        'scheduled_at' => '2026-03-13T14:00:00.000000Z',
        'sent_at' => null,
        'users' => null,
        'devices' => null,
        'created_at' => '2026-03-13T14:00:00.000000Z',
        'links' => ['self' => '/api/v2/event/1/notifications/42'],
    ]);

    expect($notif->filters)->not->toBeNull();
    expect($notif->filters->checkedIn)->toBeTrue();
    expect($notif->filters->participantType)->toHaveCount(3);
    expect($notif->filters->participantType[0])->toBe(ParticipantType::Participant);
    expect($notif->filters->participantType[1])->toBe(ParticipantType::Leader);
});

test('Participant::fromArray creates participant model', function () {
    $participant = Participant::fromArray([
        'id' => 1,
        'ref_index' => 1001,
        'participant_type' => 'participant',
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '07700900000',
        'association' => 'Group A',
        'off_site' => false,
        'checked_in_at' => '2026-03-15T09:00:00.000000Z',
        'group' => null,
    ]);

    expect($participant->id)->toBe(1);
    expect($participant->refIndex)->toBe(1001);
    expect($participant->participantType)->toBe(ParticipantType::Participant);
    expect($participant->fullName)->toBe('John Doe');
    expect($participant->email)->toBe('john@example.com');
    expect($participant->phoneNumber)->toBe('07700900000');
    expect($participant->association)->toBe('Group A');
    expect($participant->offSite)->toBeFalse();
    expect($participant->checkedInAt->format('Y-m-d'))->toBe('2026-03-15');
    expect($participant->group)->toBeNull();
});

test('Participant::fromArray with included group', function () {
    $participant = Participant::fromArray([
        'id' => 1,
        'ref_index' => 1001,
        'participant_type' => 'leader',
        'full_name' => 'Jane Smith',
        'email' => 'jane@example.com',
        'phone_number' => null,
        'association' => null,
        'off_site' => true,
        'checked_in_at' => null,
        'group' => [
            'id' => 1,
            'event_id' => 1,
            'name' => 'ACME Corp',
        ],
    ]);

    expect($participant->participantType)->toBe(ParticipantType::Leader);
    expect($participant->offSite)->toBeTrue();
    expect($participant->checkedInAt)->toBeNull();
    expect($participant->group)->not->toBeNull();
    expect($participant->group->name)->toBe('ACME Corp');
});
