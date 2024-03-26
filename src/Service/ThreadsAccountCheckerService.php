<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\ThreadsUser;
use App\Message\SendUserFederatedNotificationsMessage;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class ThreadsAccountCheckerService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
        private MessageBusInterface $bus,
    ) {
    }

    public function getOrCreateUser(string $username): ThreadsUser
    {
        $threadsUser = $this->em->getRepository(ThreadsUser::class)->findOneBy(['username' => $username]);
        if (null === $threadsUser) {
            $this->logger->notice(sprintf('📝 %s is not present in the database, adding.', $username));
            $threadsUser = new ThreadsUser(
                username: $username
            );
            $this->em->persist($threadsUser);
            $this->em->flush();
        } else {
            $this->logger->notice(sprintf('🕵️ %s is already present in the database.', $username));
        }

        return $threadsUser;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function updateUserFederated(ThreadsUser $user): void
    {
        $this->logger->notice(sprintf('👀 Checking user %s', $user->getUsername()));
        $req = $this->httpClient->request(
            method: 'GET',
            url: sprintf('https://www.threads.net/.well-known/webfinger/?resource=acct:%s@threads.net', $user->getUsername())
        );

        if (200 === $req->getStatusCode()) {
            $this->logger->notice(sprintf('🎉 %s is now federated.', $user->getUsername()));
            $user
                ->setFederated(true)
                ->setFederatedAt(new DateTimeImmutable())
            ;
            $sendNotificationsMessage = new SendUserFederatedNotificationsMessage((string) $user->getId());
            $this->bus->dispatch($sendNotificationsMessage);
        } else {
            $this->logger->notice(sprintf('🫂 %s is still not federated.', $user->getUsername()));
        }

        $user->setLastCheck(new DateTime());

        $this->em->persist($user);
        $this->em->flush();
    }
}
