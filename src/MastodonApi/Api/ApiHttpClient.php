<?php

declare(strict_types=1);

namespace App\MastodonApi\Api;

use App\MastodonApi\Exception\MastodonApiException;
use InvalidArgumentException;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function in_array;
use function is_array;

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
        if (!in_array($method, ['GET', 'DELETE', 'POST', 'PUT', 'PATCH'])) {
            throw new InvalidArgumentException(sprintf('%s is not a valid method', $method));
        }

        $this->rateLimiter->reserve(1)->wait();

        try {
            if ('GET' === $method || 'DELETE' === $method) {
                $queryString = $this->buildQueryString($data);
                $endpointWithQuery = $queryString ? $endpoint.'?'.$queryString : $endpoint;

                return $this->httpClient->request($method, $endpointWithQuery);
            }

            return $this->httpClient->request($method, $endpoint, [
                'json' => $data,
            ]);
        } catch (TransportExceptionInterface $e) {
            throw new MastodonApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array<string, string|string[]|null> $params
     */
    private function buildQueryString(array $params): string
    {
        $parts = [];
        foreach ($params as $key => $value) {
            if (null === $value) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $arrayValue) {
                    $parts[] = urlencode($key).'[]='.urlencode($arrayValue);
                }
            } else {
                $parts[] = urlencode($key).'='.urlencode($value);
            }
        }

        return implode('&', $parts);
    }
}
