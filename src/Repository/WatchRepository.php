<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Watch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Watch>
 *
 * @method Watch|null find($id, $lockMode = null, $lockVersion = null)
 * @method Watch|null findOneBy(array $criteria, array $orderBy = null)
 * @method Watch[]    findAll()
 * @method Watch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Watch::class);
    }

    public function save(Watch $watch, bool $flush = true): void
    {
        $this->getEntityManager()->persist($watch);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
