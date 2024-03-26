<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PostMastodonPrivateMentionMessage;
use App\Message\SendUserFederatedNotificationsMessage;
use App\Repository\ThreadsUserRepository;
use Doctrine\ORM\EntityNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class SendUserFederatedNotificationsMessageHandler
{
    public function __construct(
        private readonly ThreadsUserRepository $threadsUserRepository,
        private readonly LoggerInterface $logger,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(SendUserFederatedNotificationsMessage $message): void
    {
        $threadUser = $this->threadsUserRepository->findOneBy(['id' => $message->threadsUserId]);
        if (null === $threadUser) {
            throw new EntityNotFoundException(sprintf('Thread user with id %s not found', $message->threadsUserId));
        }

        $this->logger->notice(sprintf('📨 Sending notifications for newly federated account %s@threads.net', $threadUser->getUsername()));

        foreach ($threadUser->getWatchers() as $watcher) {
            $sendNotificationMessage = new PostMastodonPrivateMentionMessage(
                acct: $watcher->getWatcher(),
                message: sprintf('@%s@threads.net is now visible !', $threadUser->getUsername())
            );
            $this->bus->dispatch($sendNotificationMessage);
        }
    }
}
