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

// Group bookings
$bookings = $client->event(1)->groups()->bookings(groupId: 3)->get();
```

### Customers

```php
$customers = $client->event(1)->customers()->list()->get();

$customer = $client->event(1)->customers()->get(10);
```

### Event Stats

```php
$stats = $client->event(1)->stats();

echo $stats->totalConfirmed;
echo $stats->totalProvisional;
```

### Notifications

```php
use EventIO\ApiClient\Requests\CreateNotificationRequest;

$notifications = $client->event(1)->notifications()->list()->get();

$notification = $client->event(1)->notifications()->create(
    new CreateNotificationRequest(
        title: 'Hello',
        content: 'Notification body',
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
// or scoped to an event
$user = $client->user(eventId: 1);
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
