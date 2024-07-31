<?php

namespace App\Repository;

use App\Entity\Teams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teams>
 */
class TeamsRepository extends ServiceEntityRepository
{
    use BaseRepository;

    public const PAGE_SIZE = 10;
    public const OFFSET = 0;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teams::class);
    }

    public function getAllActiveTeams($search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        if($search){
            $qb->Where('t.id LIKE :search')
                ->orWhere('t.name LIKE :search')
                ->setParameter('search','%' .$search. '%');
        }

        $qb->andWhere('t.deleted_at IS NULL')
            ->orderBy('t.id', "DESC");

        return $qb;
    }

    public function getActiveTeam($search = null, ?int $count = null, int $offset = 0): ArrayCollection
    {
        $qb = $this->getAllActiveTeams($search);

        $qb->setFirstResult($offset);

        if ($count !== null) {
            $qb->setMaxResults($count);
        }
        return $this->paginateResults($qb, true, false);
    }

    public function getTotalCountTeam()
    {
        return $this->createQueryBuilder('t')
            ->select('count(t.id)')
            ->andWhere('t.deleted_at IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getActiveTeams()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.deleted_at IS NULL')
            ->orderBy('t.order_priority', "ASC")
            ->getQuery()->getResult();
    }

    public function findMaxOrder()
    {
        return $this->createQueryBuilder('t')
            ->select('MAX(t.order_priority)')
            ->andWhere('t.deleted_at IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
