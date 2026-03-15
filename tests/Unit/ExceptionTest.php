<?php

declare(strict_types=1);

use EventIO\ApiClient\Client;
use EventIO\ApiClient\Exceptions\AuthenticationException;
use EventIO\ApiClient\Exceptions\AuthorizationException;
use EventIO\ApiClient\Exceptions\NotFoundException;
use EventIO\ApiClient\Exceptions\ServerException;
use EventIO\ApiClient\Exceptions\ValidationException;
use EventIO\ApiClient\Tests\MockHttpFactory;

test('401 throws AuthenticationException', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json(['message' => 'Unauthenticated.'], 401),
    ]);

    $client = new Client('bad-token', 'https://api.eventio.uk/api/v2', $guzzle);
    $client->user();
})->throws(AuthenticationException::class, 'Unauthenticated.');

test('403 throws AuthorizationException', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json(['message' => 'Forbidden.'], 403),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $client->user();
})->throws(AuthorizationException::class, 'Forbidden.');

test('404 throws NotFoundException', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json(['message' => 'Not found.'], 404),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $client->event(999)->get();
})->throws(NotFoundException::class);

test('422 throws ValidationException with errors', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json([
            'message' => 'The given data was invalid.',
            'errors' => [
                'title' => ['The title field is required.'],
            ],
        ], 422),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);

    try {
        $client->event(1)->notifications()->create(
            new \EventIO\ApiClient\Requests\CreateNotificationRequest(
                title: '',
                content: 'test',
                type: \EventIO\ApiClient\Enums\NotificationType::InApp,
            ),
        );
        $this->fail('Expected ValidationException');
    } catch (ValidationException $e) {
        expect($e->errors)->toHaveKey('title');
        expect($e->errors['title'][0])->toBe('The title field is required.');
    }
});

test('500 throws ServerException', function () {
    $guzzle = MockHttpFactory::make([
        MockHttpFactory::json(['message' => 'Server Error'], 500),
    ]);

    $client = new Client('token', 'https://api.eventio.uk/api/v2', $guzzle);
    $client->user();
})->throws(ServerException::class);
