<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ThreadsUser;
use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<ThreadsUser>
 *
 * @method ThreadsUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method ThreadsUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method ThreadsUser[]    findAll()
 * @method ThreadsUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThreadsUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ThreadsUser::class);
    }

    /**
     * @return ThreadsUser[]
     *
     * @throws Exception
     */
    public function getUsersForRecheck(int $limit = 50): array
    {
        $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $date->modify('-8 hours');

        $qb = $this->createQueryBuilder('tu');

        return $qb
            ->andWhere('tu.federated = false')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->lt('tu.lastCheck', ':date'),
                    $qb->expr()->isNull('tu.lastCheck')
                )
            )
            ->setParameter('date', $date)
            ->orderBy('tu.lastCheck', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->execute()
        ;
    }
}
