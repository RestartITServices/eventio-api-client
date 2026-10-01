# EventIO PHP API Client

A typed PHP client for the [EventIO](https://eventio.uk) API (v2).

## Tech Stack

- **PHP 8.3+**
- **[Guzzle 7](https://docs.guzzlephp.org/)** — HTTP client
- **[Pest](https://pestphp.com/)** — Testing framework
- **[PHPStan](https://phpstan.org/)** — Static analysis

## Installation

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:RestartITServices/eventio-api-client.git"
        }
    ]
}
```

Then require the package:

```bash
composer require eventio/php-api-client
```

## Usage

### Initialise the Client

```php
use EventIO\ApiClient\Client;

$client = new Client(baseUrl: 'https://book.your-event.com', token: 'your-api-token');
```

### Events

```php
// List all events (paginated)
$events = $client->events()->list()->get();

foreach ($events->items() as $event) {
    echo $event->name;
}

// Get a single event
$event = $client->event(1)->get();
```

### Bookings

```php
$bookings = $client->event(1)->bookings()
    ->list()
    ->include('tickets', 'group')
    ->filter('status', 'confirmed')
    ->sort('-created_at')
    ->get();

foreach ($bookings->items() as $booking) {
    echo $booking->bookingReference;
}

// Single booking
$booking = $client->event(1)->bookings()->get(42);
```

### Tickets

```php
$tickets = $client->event(1)->tickets()->list()->get();

$ticket = $client->event(1)->tickets()->get(5);
```

### Groups

```php
$groups = $client->event(1)->groups()->list()->get();

$group = $client->event(1)->groups()->get(3);

// Group bookings, participants and the users who manage the group
$bookings = $client->event(1)->groups()->bookings(groupId: 3)->get();
$participants = $client->event(1)->groups()->participants(groupId: 3)->get();
$users = $client->event(1)->groups()->users(groupId: 3)->get();
```

### Participants

```php
$participants = $client->event(1)->participants()->list()->filter('group_id', 3)->get();

$participant = $client->event(1)->participants()->get('participant-key');
$participant = $client->event(1)->participants()->getByWristband('1001');
```

### Gates

Gates can be addressed by id or by `gate_key`.

```php
$gates = $client->event(1)->gates()->list()->filter('enabled', 1)->get();

$gate = $client->event(1)->gates()->get('main-entrance');
$occupancy = $client->event(1)->gates()->occupancy(3);
$roster = $client->event(1)->gates()->roster(3)->get();

// Passage history, newest first
$passages = $client->event(1)->gates()->passages(3, from: new DateTimeImmutable('-1 day'), perPage: 100)->get();
$passages = $client->event(1)->gates()->participantPassages(participantId: 7)->get();
```

### Event Stats

```php
$stats = $client->event(1)->stats();

echo $stats->totalConfirmed;
echo $stats->totalProvisional;
```

### Notifications

```php
use EventIO\ApiClient\Enums\NotificationType;
use EventIO\ApiClient\Requests\CreateNotificationRequest;

$notifications = $client->event(1)->notifications()->list()->get();

$notification = $client->event(1)->notifications()->create(
    new CreateNotificationRequest(
        title: 'Hello',
        content: 'Notification body',
        type: NotificationType::InApp,
    )
);
```

### Roles & Users

```php
$roles = $client->event(1)->roles()->list()->get();
$users = $client->event(1)->users()->list()->get();
```

### Authenticated User

```php
$user = $client->user();
// or scoped to an event, with the role held on it
$user = $client->user(eventId: 1);
$user = $client->event(1)->user();
```

### Service Info

```php
$client->ping();    // "API is working"
$client->version(); // API version string
```

## Query Builder

All `list()` methods return a `QueryBuilder` that supports fluent chaining:

```php
$results = $client->event(1)->bookings()
    ->list()
    ->filter('status', 'confirmed')
    ->sort('-created_at', 'booking_reference')
    ->include('tickets', 'group')
    ->get();
```

## Pagination

`get()` returns a `PaginatedResponse` that automatically fetches subsequent pages:

```php
$response = $client->event(1)->bookings()->list()->get();

// Lazy iteration — fetches pages as needed
foreach ($response->items() as $booking) {
    // ...
}

// Or load everything into an array
$all = $response->toArray();

// Get just the first item
$first = $response->first();

// Access pagination metadata
$meta = $response->meta(); // ['current_page' => 1, 'last_page' => 5, 'total' => 100, ...]
```

## Error Handling

The client throws typed exceptions for API errors:

| Exception | HTTP Status | Description |
|---|---|---|
| `AuthenticationException` | 401 | Invalid or missing API token |
| `AuthorizationException` | 403 | Insufficient permissions |
| `NotFoundException` | 404 | Resource not found |
| `ValidationException` | 422 | Validation errors (access via `->errors`) |
| `ServerException` | 500 | Server error |
| `EventIOException` | — | Base exception / generic HTTP failure |

```php
use EventIO\ApiClient\Exceptions\NotFoundException;
use EventIO\ApiClient\Exceptions\ValidationException;

try {
    $booking = $client->event(1)->bookings()->get(999);
} catch (NotFoundException $e) {
    // Resource doesn't exist
} catch (ValidationException $e) {
    $e->errors; // ['field' => ['Error message']]
}
```

## Development

```bash
# Run tests
vendor/bin/pest

# Static analysis
vendor/bin/phpstan analyse
```
