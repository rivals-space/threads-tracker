<?php

declare(strict_types=1);

namespace App\MastodonApi\Api;

use App\MastodonApi\Exception\MastodonApiException;
use InvalidArgumentException;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class ApiHttpClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private LimiterInterface $rateLimiter,
    ) {
    }

    /**
     * @param array<string, string|string[]|null> $queryParams
     *
     * @throws MastodonApiException
     */
    public function get(string $endpoint, array $queryParams = []): ResponseInterface
    {
        return $this->query('GET', $endpoint, $queryParams);
    }

    /**
     * @param array<string, string|string[]|null> $data
     *
     * @throws MastodonApiException
     */
    public function post(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->query('POST', $endpoint, $data);
    }

    /**
     * @param array<string, string|string[]|null> $data
     *
     * @throws MastodonApiException
     */
    public function put(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->query('PUT', $endpoint, $data);
    }

    /**
     * @param array<string, string|string[]|null> $data
     *
     * @throws MastodonApiException
     */
    public function patch(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->query('PATCH', $endpoint, $data);
    }

    /**
     * @param array<string, string|string[]|null> $data
     *
     * @throws MastodonApiException
     */
    public function delete(string $endpoint, array $data = []): ResponseInterface
    {
        return $this->query('DELETE', $endpoint, $data);
    }

    /**
     * @param array<string, string|string[]|null> $data
     *
     * @throws MastodonApiException
     */
    private function query(string $method, string $endpoint, array $data = []): ResponseInterface
    {
        $param = match ($method) {
            'GET', 'DELETE' => 'query',
            'POST', 'PUT', 'PATCH' => 'json',
            default => throw new InvalidArgumentException(sprintf('%s is not a valid method', $method))
        };

        $this->rateLimiter->reserve(1)->wait();

        try {
            return $this->httpClient->request($method, $endpoint, [
                $param => $data,
            ]);
        } catch (TransportExceptionInterface $e) {
            throw new MastodonApiException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
