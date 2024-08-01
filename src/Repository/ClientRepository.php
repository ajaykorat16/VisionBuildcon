<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    use BaseRepository;
    public const PAGE_SIZE = 20;
    public const OFFSET = 0;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function getAllActiveClients($search):QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');

        if($search){
            $qb->Where('c.id LIKE :search')
                ->orWhere('c.name LIKE :search')
                ->orWhere('c.description LIKE :search')
                ->setParameter('search','%' .$search. '%');
        }

        $qb->andWhere('c.deletedAt IS NULL')
            ->orderBy('c.id', "DESC");

        return $qb;
    }
    public function getActiveClients($search = null, ?int $count = null, int $offset = 0): ArrayCollection
    {
        $qb = $this->getAllActiveClients($search);

        $qb->setFirstResult($offset);

        if ($count !== null) {
            $qb->setMaxResults($count);
        }
        return $this->paginateResults($qb, true, false);
    }

    public function getTotalCountClients()
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getClients()
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.deletedAt IS NULL')
            ->getQuery()->getResult();
    }
}
