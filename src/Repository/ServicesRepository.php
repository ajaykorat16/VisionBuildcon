<?php

namespace App\Repository;

use App\Entity\Services;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Services>
 */
class ServicesRepository extends ServiceEntityRepository
{
    use BaseRepository;
    public const PAGE_SIZE = 10;
    public const OFFSET = 0;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Services::class);
    }

    //    /**
    //     * @return Services[] Returns an array of Services objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Services
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAllActiveServices($search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('s');

        if($search){
            $qb->Where('s.id LIKE :search')
                ->orWhere('s.name LIKE :search')
                ->orWhere('s.description LIKE :search')
                ->setParameter('search','%' .$search. '%');
        }

        $qb->andWhere('s.deleted_at IS NULL')
            ->orderBy('s.id', "DESC");

        return $qb;
    }

    public function getActiveServices($search = null, ?int $count = null, int $offset = 0): ArrayCollection
    {
        $qb = $this->getAllActiveServices($search);

        $qb->setFirstResult($offset);

        if ($count !== null) {
            $qb->setMaxResults($count);
        }
        return $this->paginateResults($qb, true, false);
    }

    public function getTotalCountServices()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->andWhere('s.deleted_at IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findActiveServices()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.deleted_at IS NULL')
            ->orderBy('s.id', "ASC")
            ->getQuery()->getResult();
    }

}
