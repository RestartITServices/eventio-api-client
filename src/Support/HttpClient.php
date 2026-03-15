<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Support;

use EventIO\ApiClient\Exceptions\AuthenticationException;
use EventIO\ApiClient\Exceptions\AuthorizationException;
use EventIO\ApiClient\Exceptions\EventIOException;
use EventIO\ApiClient\Exceptions\NotFoundException;
use EventIO\ApiClient\Exceptions\ServerException;
use EventIO\ApiClient\Exceptions\ValidationException;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException as GuzzleServerException;

final class HttpClient
{
    private Guzzle $guzzle;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $token,
        ?Guzzle $guzzle = null,
        ?string $tenant = null,
    ) {
        $this->guzzle = $guzzle ?? new Guzzle([
            'base_uri' => rtrim($this->baseUrl, '/') . '/',
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ] + ($tenant ? ['X-Tenant' => $tenant] : []),
        ]);
    }

    /**
     * @param array<string, mixed> $query
     * @return array<string, mixed>
     */
    public function get(string $uri, array $query = []): array
    {
        return $this->request('GET', $uri, ['query' => $query]);
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function post(string $uri, array $data = []): array
    {
        return $this->request('POST', $uri, ['json' => $data]);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function request(string $method, string $uri, array $options = []): array
    {
        try {
            $response = $this->guzzle->request($method, ltrim($uri, '/'), $options);

            /** @var array<string, mixed> */
            return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        } catch (ClientException $e) {
            $this->handleClientException($e);
        } catch (GuzzleServerException $e) {
            $body = (string) $e->getResponse()->getBody();
            throw new ServerException(
                message: 'Server error: ' . $e->getMessage(),
                code: $e->getResponse()->getStatusCode(),
                previous: $e,
                errorBody: $body,
            );
        } catch (GuzzleException $e) {
            throw new EventIOException(
                message: 'HTTP request failed: ' . $e->getMessage(),
                previous: $e,
            );
        }
    }

    /**
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws NotFoundException
     * @throws ValidationException
     */
    private function handleClientException(ClientException $e): never
    {
        $status = $e->getResponse()->getStatusCode();
        $body = (string) $e->getResponse()->getBody();

        /** @var array{message?: string, errors?: array<string, list<string>>} $decoded */
        $decoded = json_decode($body, true) ?? [];
        $message = $decoded['message'] ?? $e->getMessage();

        throw match ($status) {
            401 => new AuthenticationException($message, $status, $e, $body),
            403 => new AuthorizationException($message, $status, $e, $body),
            404 => new NotFoundException($message, $status, $e, $body),
            422 => new ValidationException(
                message: $message,
                code: $status,
                previous: $e,
                errorBody: $body,
                errors: $decoded['errors'] ?? [],
            ),
            default => new EventIOException($message, $status, $e, $body),
        };
    }
}
