<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Watch;
use App\Exception\AlreadyWatchingAccountException;
use App\Repository\WatchRepository;

class WatchThreadsAccountService
{
    public function __construct(
        private readonly WatchRepository $watchRepository,
        private readonly ThreadsAccountCheckerService $accountCheckerService,
    ) {
    }

    /**
     * @throws AlreadyWatchingAccountException
     */
    public function addWatch(string $acct, string $threadUserName): Watch
    {
        $watch = $this->watchRepository->findOneBy(['watcher' => $acct]);
        if (null === $watch) {
            $watch = new Watch($acct);
        }

        if ($this->isAlreadyWatching($acct, $threadUserName)) {
            throw new AlreadyWatchingAccountException(sprintf('Already watching account %s', $threadUserName));
        }

        $watch->addWatched(
            $this->accountCheckerService->getOrCreateUser($threadUserName)
        );

        $this->watchRepository->save($watch);

        return $watch;
    }

    public function isAlreadyWatching(string $watcher, string $watched): bool
    {
        return null !== $this->watchRepository->findOneBy([
            'watcher' => $watcher,
            'watched' => $watched,
        ]);
    }
}
