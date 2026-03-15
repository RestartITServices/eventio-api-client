<?php

declare(strict_types=1);

namespace EventIO\ApiClient\Support;

/**
 * @template T of object
 */
final class QueryBuilder
{
    /**
     * @param class-string<T> $modelClass
     * @param array<string, mixed> $filters
     * @param list<string> $sorts
     * @param list<string> $includes
     * @param array<string, mixed> $params
     */
    private function __construct(
        private readonly HttpClient $http,
        private readonly string $uri,
        private readonly string $modelClass,
        private readonly array $filters = [],
        private readonly array $sorts = [],
        private readonly array $includes = [],
        private readonly array $params = [],
    ) {}

    /**
     * @template TModel of object
     * @param class-string<TModel> $modelClass
     * @return self<TModel>
     */
    public static function make(HttpClient $http, string $uri, string $modelClass): self
    {
        return new self($http, $uri, $modelClass);
    }

    /**
     * @return self<T>
     */
    public function filter(string $field, mixed $value): self
    {
        return new self(
            $this->http,
            $this->uri,
            $this->modelClass,
            [...$this->filters, $field => $value],
            $this->sorts,
            $this->includes,
            $this->params,
        );
    }

    /**
     * @return self<T>
     */
    public function sort(string ...$fields): self
    {
        return new self(
            $this->http,
            $this->uri,
            $this->modelClass,
            $this->filters,
            array_values([...$this->sorts, ...$fields]),
            $this->includes,
            $this->params,
        );
    }

    /**
     * @return self<T>
     */
    public function include(string ...$relations): self
    {
        return new self(
            $this->http,
            $this->uri,
            $this->modelClass,
            $this->filters,
            $this->sorts,
            array_values([...$this->includes, ...$relations]),
            $this->params,
        );
    }

    /**
     * @return self<T>
     */
    public function param(string $key, mixed $value): self
    {
        return new self(
            $this->http,
            $this->uri,
            $this->modelClass,
            $this->filters,
            $this->sorts,
            $this->includes,
            [...$this->params, $key => $value],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toQuery(): array
    {
        $query = $this->params;

        foreach ($this->filters as $field => $value) {
            $query["filter[{$field}]"] = $value;
        }

        if ($this->sorts !== []) {
            $query['sort'] = implode(',', $this->sorts);
        }

        if ($this->includes !== []) {
            $query['include'] = implode(',', $this->includes);
        }

        return $query;
    }

    /**
     * @return PaginatedResponse<T>
     */
    public function get(): PaginatedResponse
    {
        return new PaginatedResponse($this->http, $this->uri, $this->toQuery(), $this->modelClass);
    }
}
