<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CheckThreadUserMessage;
use App\Service\ThreadsAccountCheckerService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

#[AsMessageHandler]
final class CheckThreadUserMessageHandler
{
    public function __construct(
        private readonly ThreadsAccountCheckerService $accountCheckerService,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(CheckThreadUserMessage $message): void
    {
        $user = $this->accountCheckerService->getOrCreateUser($message->username);
        $this->accountCheckerService->updateUserFederated($user);
    }
}
