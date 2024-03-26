<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CheckThreadUserMessage;
use App\Message\StartBatchCheckingMessage;
use App\Repository\ThreadsUserRepository;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

use function count;

#[AsMessageHandler]
final readonly class StartBatchCheckingMessageHandler
{
    public function __construct(
        private ThreadsUserRepository $userRepository,
        private MessageBusInterface $bus,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Exception
     */
    public function __invoke(StartBatchCheckingMessage $message): void
    {
        $this->logger->notice('🕵️ Fetching threads user to check');

        $accounts = $this->userRepository->getUsersForRecheck();

        $this->logger->notice(sprintf('✉️ Dispatched %s users for checking', count($accounts)));

        foreach ($accounts as $account) {
            $checkMessage = new CheckThreadUserMessage($account->getUsername());
            $this->bus->dispatch($checkMessage);
        }
    }
}
