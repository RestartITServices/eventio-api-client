<?php

declare(strict_types=1);

use EventIO\ApiClient\Client;
use EventIO\ApiClient\Enums\BookingStatus;
use EventIO\ApiClient\Enums\NotificationStatus;
use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Enums\ParticipantType;
use EventIO\ApiClient\Models\Booking;
use EventIO\ApiClient\Models\Customer;
use EventIO\ApiClient\Models\Event;
use EventIO\ApiClient\Models\EventRole;
use EventIO\ApiClient\Models\EventUser;
use EventIO\ApiClient\Models\Group;
use EventIO\ApiClient\Models\Notification;
use EventIO\ApiClient\Models\Ticket;
use EventIO\ApiClient\Requests\CreateNotificationRequest;
use EventIO\ApiClient\Requests\NotificationFilters;
use EventIO\ApiClient\Tests\MockHttpFactory;

test('client fetches user', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
        ]),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $user = $client->user();

    expect($user->id)->toBe(1);
    expect($user->name)->toBe('John Doe');
    expect((string) $history[0]['request']->getUri())->toContain('user');
});

test('client fetches user with event role', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'],
            'event_role' => ['id' => 1, 'title' => 'Event Manager'],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $user = $client->user(eventId: 1);

    expect($user->eventRole)->not->toBeNull();
    expect($user->eventRole->title)->toBe('Event Manager');
});

test('client lists events with filters', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'slug' => 'conference-2026', 'name' => 'Annual Conference 2026', 'start_date' => '2026-06-15', 'end_date' => '2026-06-17'],
            ],
        ]),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $events = $client->events()->list()
        ->filter('name', 'Annual')
        ->sort('-start_date')
        ->include('tickets')
        ->get()
        ->toArray();

    expect($events)->toHaveCount(1);
    expect($events[0])->toBeInstanceOf(Event::class);
    expect($events[0]->name)->toBe('Annual Conference 2026');

    $query = $history[0]['request']->getUri()->getQuery();
    expect($query)->toContain('filter%5Bname%5D=Annual');
    expect($query)->toContain('sort=-start_date');
    expect($query)->toContain('include=tickets');
});

test('client gets single event', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => ['id' => 1, 'slug' => 'conference-2026', 'name' => 'Annual Conference 2026', 'start_date' => '2026-06-15', 'end_date' => '2026-06-17'],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $event = $client->event(1)->get();

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->slug)->toBe('conference-2026');
});

test('client gets event stats', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'event_id' => 1,
            'event_name' => 'Annual Conference 2026',
            'stats' => [
                ['id' => 1, 'participant_type' => 'adult', 'title' => 'Adult Ticket', 'total_confirmed_tickets_sold' => 150, 'total_provisional_tickets_sold' => 25],
            ],
            'total_confirmed' => 150,
            'total_provisional' => 175,
            'generated_at' => '2026-01-15T10:30:00+00:00',
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $stats = $client->event(1)->stats();

    expect($stats->totalConfirmed)->toBe(150);
    expect($stats->stats)->toHaveCount(1);
});

test('client lists bookings with includes', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                [
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
                        'customer' => ['id' => 1, 'full_name' => 'Jane Smith', 'email_address' => 'jane@acme.com'],
                    ],
                ],
            ],
        ]),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $bookings = $client->event(1)->bookings()->list()
        ->filter('status', 'confirmed')
        ->include('tickets', 'group.customer')
        ->get()
        ->toArray();

    expect($bookings)->toHaveCount(1);
    expect($bookings[0])->toBeInstanceOf(Booking::class);
    expect($bookings[0]->status)->toBe(BookingStatus::Confirmed);
    expect($bookings[0]->tickets)->toHaveCount(1);
    expect($bookings[0]->group->customer->fullName)->toBe('Jane Smith');

    $uri = (string) $history[0]['request']->getUri();
    expect($uri)->toContain('event/1/bookings');
});

test('client lists tickets', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'event_id' => 1, 'title' => 'Adult Ticket', 'participant_type' => 'participant', 'price' => '99.00'],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $tickets = $client->event(1)->tickets()->list()->get()->toArray();

    expect($tickets)->toHaveCount(1);
    expect($tickets[0])->toBeInstanceOf(Ticket::class);
});

test('client gets single ticket', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => ['id' => 1, 'event_id' => 1, 'title' => 'Adult Ticket', 'participant_type' => 'participant', 'price' => '99.00'],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $ticket = $client->event(1)->tickets()->get(1);

    expect($ticket)->toBeInstanceOf(Ticket::class);
    expect($ticket->title)->toBe('Adult Ticket');
});

test('client lists groups', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'event_id' => 1, 'name' => 'ACME Corporation', 'association' => 'Corporate Partner'],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $groups = $client->event(1)->groups()->list()->get()->toArray();

    expect($groups)->toHaveCount(1);
    expect($groups[0])->toBeInstanceOf(Group::class);
});

test('client lists customers', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'full_name' => 'Jane Smith', 'email_address' => 'jane@example.com', 'post_code' => 'SW1A 1AA'],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $customers = $client->event(1)->customers()->list()->get()->toArray();

    expect($customers)->toHaveCount(1);
    expect($customers[0])->toBeInstanceOf(Customer::class);
});

test('client lists roles with permissions', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'Event Manager',
                    'permissions' => [
                        ['id' => 1, 'event_role_id' => 1, 'area' => 'event', 'permission' => 'admin'],
                    ],
                ],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $roles = $client->event(1)->roles()->list()->include('permissions')->get()->toArray();

    expect($roles)->toHaveCount(1);
    expect($roles[0])->toBeInstanceOf(EventRole::class);
    expect($roles[0]->permissions)->toHaveCount(1);
});

test('client lists event users', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                [
                    'id' => 1,
                    'user_id' => 5,
                    'event_id' => 1,
                    'active' => true,
                    'user' => ['id' => 5, 'name' => 'John Doe', 'email' => 'john@example.com'],
                    'role' => ['id' => 1, 'title' => 'Event Manager'],
                ],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $users = $client->event(1)->users()->list()->include('user', 'role')->get()->toArray();

    expect($users)->toHaveCount(1);
    expect($users[0])->toBeInstanceOf(EventUser::class);
    expect($users[0]->user->name)->toBe('John Doe');
});

test('client lists notifications', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                [
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
                ],
            ],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $notifications = $client->event(1)->notifications()->list()->get()->toArray();

    expect($notifications)->toHaveCount(1);
    expect($notifications[0])->toBeInstanceOf(Notification::class);
    expect($notifications[0]->status)->toBe(NotificationStatus::Sent);
});

test('client creates notification', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                'id' => 42,
                'event_id' => 1,
                'title' => 'Weather Alert',
                'content' => 'Heavy rain expected.',
                'type' => 'incident',
                'status' => 'scheduled',
                'filters' => [
                    'checked_in' => true,
                    'participant_type' => ['participant', 'leader'],
                ],
                'scheduled_at' => '2026-03-13T14:00:00.000000Z',
                'sent_at' => null,
                'users' => null,
                'devices' => null,
                'created_at' => '2026-03-13T14:00:00.000000Z',
                'links' => ['self' => '/api/v2/event/1/notifications/42'],
            ],
        ], 202),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $notification = $client->event(1)->notifications()->create(
        new CreateNotificationRequest(
            title: 'Weather Alert',
            content: 'Heavy rain expected.',
            type: NotificationType::Incident,
            filters: new NotificationFilters(
                checkedIn: true,
                participantType: [ParticipantType::Participant, ParticipantType::Leader],
            ),
        ),
    );

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->id)->toBe(42);
    expect($notification->status)->toBe(NotificationStatus::Scheduled);
    expect($notification->filters)->not->toBeNull();
    expect($notification->filters->checkedIn)->toBeTrue();

    // Verify the POST body was correct
    $body = json_decode((string) $history[0]['request']->getBody(), true);
    expect($body['title'])->toBe('Weather Alert');
    expect($body['type'])->toBe('incident');
    expect($body['filters']['checked_in'])->toBeTrue();
});

test('client handles pagination via generator', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'slug' => 'event-1', 'name' => 'Event 1', 'start_date' => '2026-06-15', 'end_date' => '2026-06-17'],
                ['id' => 2, 'slug' => 'event-2', 'name' => 'Event 2', 'start_date' => '2026-07-01', 'end_date' => '2026-07-03'],
            ],
            'meta' => ['current_page' => 1, 'last_page' => 2],
        ]),
        MockHttpFactory::json([
            'data' => [
                ['id' => 3, 'slug' => 'event-3', 'name' => 'Event 3', 'start_date' => '2026-08-01', 'end_date' => '2026-08-03'],
            ],
            'meta' => ['current_page' => 2, 'last_page' => 2],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $events = $client->events()->list()->get()->toArray();

    expect($events)->toHaveCount(3);
    expect($events[0]->name)->toBe('Event 1');
    expect($events[2]->name)->toBe('Event 3');
});

test('first() returns first item without fetching all pages', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'slug' => 'event-1', 'name' => 'Event 1', 'start_date' => '2026-06-15', 'end_date' => '2026-06-17'],
            ],
            'meta' => ['current_page' => 1, 'last_page' => 5],
        ]),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $event = $client->events()->list()->get()->first();

    expect($event)->toBeInstanceOf(Event::class);
    expect($event->name)->toBe('Event 1');
});

test('group bookings endpoint', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                [
                    'id' => 1,
                    'event_id' => 1,
                    'booking_type' => 'participant',
                    'booking_reference' => 'EVT-GRP1',
                    'status' => 'confirmed',
                    'created_at' => '2026-01-10T14:30:00.000000Z',
                ],
            ],
        ]),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $bookings = $client->event(1)->groups()->bookings(5)->get()->toArray();

    expect($bookings)->toHaveCount(1);
    $uri = (string) $history[0]['request']->getUri();
    expect($uri)->toContain('event/1/groups/5/bookings');
});

test('booking tickets endpoint', function () {
    $history = [];
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'data' => [
                ['id' => 1, 'booking_id' => 1, 'ticket_id' => 1, 'ticket_title' => 'Adult', 'participant_type' => 'participant', 'qty' => 2, 'price' => '198.00'],
            ],
        ]),
    ], $history);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $tickets = $client->event(1)->bookings()->tickets(1)->list()->get()->toArray();

    expect($tickets)->toHaveCount(1);
    $uri = (string) $history[0]['request']->getUri();
    expect($uri)->toContain('event/1/bookings/1/tickets');
});
