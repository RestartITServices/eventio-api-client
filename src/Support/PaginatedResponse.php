<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Support;

use Generator;

/**
 * @template T of object
 */
final class PaginatedResponse
{
    /** @var list<T>|null */
    private ?array $firstPageItems = null;

    /** @var array<string, mixed>|null */
    private ?array $firstPageMeta = null;

    /**
     * @param class-string<T> $modelClass
     * @param array<string, mixed> $query
     */
    public function __construct(
        private readonly HttpClient $http,
        private readonly string $uri,
        private readonly array $query,
        private readonly string $modelClass,
    ) {}

    /**
     * @return Generator<int, T>
     */
    public function items(): Generator
    {
        $this->loadFirstPage();

        /** @var T $item */
        foreach ($this->firstPageItems ?? [] as $item) {
            yield $item;
        }

        $meta = $this->firstPageMeta;
        $currentPage = $meta['current_page'] ?? 1;
        $lastPage = $meta['last_page'] ?? 1;

        while ($currentPage < $lastPage) {
            $currentPage++;
            $query = array_merge($this->query, ['page' => $currentPage]);
            $response = $this->http->get($this->uri, $query);

            /** @var list<array<string, mixed>> $data */
            $data = $response['data'] ?? [];

            foreach ($data as $item) {
                yield $this->hydrate($item);
            }
        }
    }

    /**
     * @return list<T>
     */
    public function toArray(): array
    {
        return iterator_to_array($this->items(), false);
    }

    /**
     * @return T|null
     */
    public function first(): ?object
    {
        foreach ($this->items() as $item) {
            return $item;
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function meta(): ?array
    {
        $this->loadFirstPage();

        return $this->firstPageMeta;
    }

    private function loadFirstPage(): void
    {
        if ($this->firstPageItems !== null) {
            return;
        }

        $response = $this->http->get($this->uri, $this->query);

        /** @var list<array<string, mixed>> $data */
        $data = $response['data'] ?? [];

        $this->firstPageItems = array_map(
            fn (array $item) => $this->hydrate($item),
            $data,
        );

        /** @var array<string, mixed> $meta */
        $meta = $response['meta'] ?? null;
        $this->firstPageMeta = $meta;
    }

    /**
     * @param array<string, mixed> $data
     * @return T
     */
    private function hydrate(array $data): object
    {
        /** @var T */
        return ($this->modelClass)::fromArray($data); // @phpstan-ignore staticMethod.notFound
    }
}
