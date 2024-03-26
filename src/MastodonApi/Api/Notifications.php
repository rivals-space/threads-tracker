<?php

declare(strict_types=1);

namespace App\MastodonApi\Api;

use App\MastodonApi\Dto\Notification;
use App\MastodonApi\Exception\MastodonApiException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Notifications extends AbstractApi
{
    /**
     * @param array<array-key, string> $types
     * @param array<array-key, string> $excludeTypes
     *
     * @return Notification[]
     *
     * @throws MastodonApiException
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function getAllNotifications(
        ?string $maxId = null,
        ?string $sinceId = null,
        ?string $minId = null,
        ?string $accountId = null,
        ?int $limit = 40,
        array $types = [],
        array $excludeTypes = [],
    ): array {
        $notifications = $this->client->get(
            endpoint: '/api/v1/notifications',
            queryParams: [
                'max_id' => $maxId,
                'since_id' => $sinceId,
                'min_id' => $minId,
                'account_id' => $accountId,
                'limit' => (string) $limit,
                'types' => $types,
                'exclude_types' => $excludeTypes,
            ]
        );
        $content = $notifications->getContent();

        /** @var Notification[] $decodedContent */
        $decodedContent = $this->serializer->deserialize($content, Notification::class.'[]', 'json');

        return $decodedContent;
    }

    /**
     * @throws MastodonApiException
     */
    public function dismissNotification(string $notificationId): void
    {
        $this->client->post("/api/v1/notifications/$notificationId/dismiss");
    }
}
