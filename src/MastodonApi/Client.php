<?php

declare(strict_types=1);

namespace App\MastodonApi;

use App\MastodonApi\Api\AbstractApi;
use App\MastodonApi\Api\ApiHttpClient;
use App\MastodonApi\Api\Notifications;
use InvalidArgumentException;
use SensitiveParameter;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class Client
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private RateLimiterFactory $mastodonApiLimiter,
        private string $mastodonServer,
        #[SensitiveParameter]
        private string $mastodonToken,
    ) {
    }

    public function api(string $name): AbstractApi
    {
        $httpClient = $this->buildHttpClient();

        return match ($name) {
            'notifications', 'notification', 'notif' => new Notifications($httpClient, $this->serializer),
            default => throw new InvalidArgumentException(sprintf('%s is not a valid API', $name)),
        };
    }

    private function buildHttpClient(): ApiHttpClient
    {
        return new ApiHttpClient(
            httpClient: $this->httpClient->withOptions(
                [
                    'base_uri' => sprintf('https://%s', $this->mastodonServer),
                    'auth_bearer' => $this->mastodonToken,
                ]
            ),
            rateLimiter: $this->mastodonApiLimiter->create(),
        );
    }
}
