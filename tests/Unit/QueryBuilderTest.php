<?php

declare(strict_types=1);

use EventIO\ApiClient\Support\QueryBuilder;
use EventIO\ApiClient\Support\HttpClient;
use EventIO\ApiClient\Models\Event;

test('filter adds filter query parameters', function () {
    $history = [];
    $guzzle = \EventIO\ApiClient\Tests\MockHttpFactory::make([
        \EventIO\ApiClient\Tests\MockHttpFactory::json(['data' => []]),
    ], $history);
    $http = new HttpClient('https://api.eventio.uk/api/v2', 'token', $guzzle);

    $builder = QueryBuilder::make($http, 'events', Event::class)
        ->filter('name', 'Annual')
        ->filter('slug', 'conference-2026');

    expect($builder->toQuery())->toBe([
        'filter[name]' => 'Annual',
        'filter[slug]' => 'conference-2026',
    ]);
});

test('sort adds comma-separated sort parameter', function () {
    $history = [];
    $guzzle = \EventIO\ApiClient\Tests\MockHttpFactory::make([
        \EventIO\ApiClient\Tests\MockHttpFactory::json(['data' => []]),
    ], $history);
    $http = new HttpClient('https://api.eventio.uk/api/v2', 'token', $guzzle);

    $builder = QueryBuilder::make($http, 'events', Event::class)
        ->sort('-start_date', 'name');

    expect($builder->toQuery())->toBe([
        'sort' => '-start_date,name',
    ]);
});

test('include adds comma-separated include parameter', function () {
    $history = [];
    $guzzle = \EventIO\ApiClient\Tests\MockHttpFactory::make([
        \EventIO\ApiClient\Tests\MockHttpFactory::json(['data' => []]),
    ], $history);
    $http = new HttpClient('https://api.eventio.uk/api/v2', 'token', $guzzle);

    $builder = QueryBuilder::make($http, 'events', Event::class)
        ->include('tickets', 'bookings');

    expect($builder->toQuery())->toBe([
        'include' => 'tickets,bookings',
    ]);
});

test('query builder is immutable', function () {
    $history = [];
    $guzzle = \EventIO\ApiClient\Tests\MockHttpFactory::make([
        \EventIO\ApiClient\Tests\MockHttpFactory::json(['data' => []]),
    ], $history);
    $http = new HttpClient('https://api.eventio.uk/api/v2', 'token', $guzzle);

    $original = QueryBuilder::make($http, 'events', Event::class);
    $filtered = $original->filter('name', 'Test');

    expect($original->toQuery())->toBe([]);
    expect($filtered->toQuery())->toBe(['filter[name]' => 'Test']);
});

test('combined filter, sort, include, and param', function () {
    $history = [];
    $guzzle = \EventIO\ApiClient\Tests\MockHttpFactory::make([
        \EventIO\ApiClient\Tests\MockHttpFactory::json(['data' => []]),
    ], $history);
    $http = new HttpClient('https://api.eventio.uk/api/v2', 'token', $guzzle);

    $query = QueryBuilder::make($http, 'events', Event::class)
        ->filter('name', 'Annual')
        ->sort('-start_date')
        ->include('tickets')
        ->param('no-cache', '1')
        ->toQuery();

    expect($query)->toBe([
        'no-cache' => '1',
        'filter[name]' => 'Annual',
        'sort' => '-start_date',
        'include' => 'tickets',
    ]);
});
