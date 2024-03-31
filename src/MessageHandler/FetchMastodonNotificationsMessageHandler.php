<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\MastodonApi\Api\Notifications;
use App\MastodonApi\Client;
use App\MastodonApi\Exception\MastodonApiException;
use App\Message\FetchMastodonNotificationsMessage;
use App\Message\ProcessMastodonNotificationMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

use function count;

#[AsMessageHandler]
final class FetchMastodonNotificationsMessageHandler
{
    public function __construct(
        private readonly Client $mastodonApi,
        private readonly MessageBusInterface $bus,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws MastodonApiException
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function __invoke(FetchMastodonNotificationsMessage $message): void
    {
        $this->logger->notice('🔄 Checking for new mentions..');
        /** @var Notifications $notificationApi */
        $notificationApi = $this->mastodonApi->api('notifications');

        $notifications = $notificationApi->getAllNotifications(
            types: ['mention']
        );

        $this->logger->notice(sprintf('🔔 %s new mentions', count($notifications)));

        foreach ($notifications as $notification) {
            $this->logger->notice(sprintf('✉️ Dispatching notification %s', $notification->id));
            $this->bus->dispatch(
                new ProcessMastodonNotificationMessage($notification)
            );
        }
    }
}
