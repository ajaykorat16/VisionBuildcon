<?php

namespace App\Repository;

use App\Entity\Request;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Request>
 */
class RequestRepository extends ServiceEntityRepository
{
    use BaseRepository;
    public const PAGE_SIZE = 20;
    public const OFFSET = 0;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Request::class);
    }

    public function getRequests(?int $count = null, int $offset = 0): ArrayCollection
    {
        $qb = $this->createQueryBuilder('r');
        $qb->setFirstResult($offset);
    
        $qb->Where('r.deletedAt IS NULL')
           ->orderBy('r.id', 'DESC');
    
        if ($count !== null) {
            $qb->setMaxResults($count);
        }
    
        return $this->paginateResults($qb, true, false);
    }
    
    public function getTotalCountRequest()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->Where('r.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
