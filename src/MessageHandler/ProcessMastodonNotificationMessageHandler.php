<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Enum\Operation;
use App\Exception\AlreadyWatchingAccountException;
use App\Exception\InvalidStatusCommandException;
use App\MastodonApi\Api\Notifications;
use App\MastodonApi\Client;
use App\MastodonApi\Exception\MastodonApiException;
use App\Message\PostMastodonPrivateMentionMessage;
use App\Message\ProcessMastodonNotificationMessage;
use App\Service\WatchThreadsAccountService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class ProcessMastodonNotificationMessageHandler
{
    public function __construct(
        private Client $mastodonApi,
        private MessageBusInterface $bus,
        private WatchThreadsAccountService $watchThreadsAccountService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws MastodonApiException
     */
    public function __invoke(ProcessMastodonNotificationMessage $message): void
    {
        $notificationId = $message->notification->id;
        $acct = $message->notification->account->acct;
        $statusContent = $message->notification->status->content;

        $this->logger->info(sprintf('Processing notification %s from %s', $notificationId, $acct));

        try {
            $operation = $this->extractOperation($statusContent);
            match ($operation) {
                Operation::WATCH => $this->handleWatchAccount($acct, $this->extractThreadAccount($statusContent)),
                Operation::UNWATCH => $this->handleUnwatchAccount($acct, $this->extractThreadAccount($statusContent)),
                Operation::LIST => $this->handleList($acct),
                Operation::UNWATCH_ALL => $this->handleUnwatchAll($acct)
            };
        } catch (InvalidStatusCommandException $e) {
            $this->handleError($acct, $e->getMessage());
        }
        $this->dismissNotification($notificationId);
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function extractOperation(string $statusContent): Operation
    {
        return match (true) {
            str_contains($statusContent, Operation::WATCH->value) => Operation::WATCH,
            str_contains($statusContent, Operation::UNWATCH->value) => Operation::UNWATCH,
            str_contains($statusContent, Operation::LIST->value) => Operation::LIST,
            str_contains($statusContent, Operation::UNWATCH_ALL->value) => Operation::UNWATCH_ALL,
            default => throw new InvalidStatusCommandException('Invalid operation')
        };
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function extractThreadAccount(string $statusContent): string
    {
        preg_match('/threads.net\/@(?<username>[\w\.]+)\??/', $statusContent, $matches);
        if (!isset($matches['username'])) {
            throw new InvalidStatusCommandException('Threads profile URL invalid or missing');
        }

        return $matches['username'];
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function handleWatchAccount(string $acct, string $threadAccount): void
    {
        $this->logger->notice(sprintf('➕ %s requesting to watch %s', $acct, $threadAccount));

        try {
            $this->watchThreadsAccountService->addWatch($acct, $threadAccount);
        } catch (AlreadyWatchingAccountException $e) {
            throw new InvalidStatusCommandException($e->getMessage(), $e->getCode(), $e);
        }

        $this->logger->notice(sprintf('✔️ %s is now watching %s', $acct, $threadAccount));
        $this->bus->dispatch(
            new PostMastodonPrivateMentionMessage(
                acct: $acct,
                message: sprintf('Now watching for %s', $threadAccount)
            )
        );
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function handleUnwatchAccount(string $acct, string $threadAccount): void
    {
        // todo : implement
        throw new InvalidStatusCommandException('Not implemented yet');
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function handleUnwatchAll(string $acct): void
    {
        // todo : implement
        throw new InvalidStatusCommandException('Not implemented yet');
    }

    /**
     * @throws InvalidStatusCommandException
     */
    private function handleList(string $acct): void
    {
        // todo : implement
        throw new InvalidStatusCommandException('Not implemented yet');
    }

    private function handleError(string $acct, string $error): void
    {
        $this->bus->dispatch(
            new PostMastodonPrivateMentionMessage(
                acct: $acct,
                message: $error
            )
        );
    }

    /**
     * @throws MastodonApiException
     */
    private function dismissNotification(string $notificationId): void
    {
        /** @var Notifications $notificationApi */
        $notificationApi = $this->mastodonApi->api('notifications');

        $this->logger->notice(sprintf('🗑️ Deleting notification %s', $notificationId));
        $notificationApi->dismissNotification($notificationId);
    }
}
