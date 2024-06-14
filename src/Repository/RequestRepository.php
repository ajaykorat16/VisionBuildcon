<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    use BaseRepository;
    public const PAGE_SIZE = 10;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    //    /**
    //     * @return Request[] Returns an array of Request objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Request
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findAllRequests(int $offset = 0, ?int $count = null): ArrayCollection
    {
        $qb = $this->createQueryBuilder('r');
        $qb->setFirstResult($offset);

        if ($count !== null) {
            $qb->setMaxResults($count);
        }
        return $this->paginateResults($qb, true, false);
    }

    public function findAllRequestsCount()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
//            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
