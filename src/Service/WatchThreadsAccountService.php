<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Watch;
use App\Exception\AlreadyWatchingAccountException;
use App\Repository\WatchRepository;

readonly class WatchThreadsAccountService
{
    public function __construct(
        private WatchRepository $watchRepository,
        private ThreadsAccountCheckerService $accountCheckerService,
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

        $threadsUser = $this->accountCheckerService->getOrCreateUser($threadUserName);

        if ($watch->getWatched()->contains($threadsUser)) {
            throw new AlreadyWatchingAccountException(sprintf('Already watching account %s', $threadUserName));
        }

        $watch->addWatched($threadsUser);

        $this->watchRepository->save($watch);

        return $watch;
    }
}
