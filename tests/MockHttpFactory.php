<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Tests;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

final class MockHttpFactory
{
    /**
     * @param list<Response> $responses
     * @param list<array<string, mixed>> $history
     */
    public static function make(array $responses, array &$history = []): Guzzle
    {
        $mock = new MockHandler($responses);
        $stack = HandlerStack::create($mock);
        $stack->push(Middleware::history($history));

        return new Guzzle([
            'handler' => $stack,
            'base_uri' => 'https://api.eventio.uk/api/v2/',
            'headers' => [
                'Authorization' => 'Bearer test-token',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public static function json(mixed $body, int $status = 200): Response
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json'],
            json_encode($body, JSON_THROW_ON_ERROR),
        );
    }
}
