<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    use BaseRepository;
    public const PAGE_SIZE = 10;
    public const OFFSET = 0;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAllActiveProjects($search) :QueryBuilder
    {
        $qb = $this->createQueryBuilder('p');

        if($search){
            $qb->Where('p.id LIKE :search')
                ->orWhere('p.name LIKE :search')
                ->orWhere('p.description LIKE :search')
                ->setParameter('search','%' .$search. '%');
        }

        $qb->andWhere('p.deletedAt IS NULL')
            ->orderBy('p.id', "DESC");

        return $qb;
    }
    public function getActiveProjects($search = null, ?int $count = null, int $offset = 0): ArrayCollection
    {
        $qb = $this->getAllActiveProjects($search);

        $qb->setFirstResult($offset);

        if ($count !== null) {
            $qb->setMaxResults($count);
        }

        return $this->paginateResults($qb, true, false);
    }

    public function getTotalCountsProjects()
    {
        return $this->createQueryBuilder('p')
            ->select('count(p.id)')
            ->andWhere('p.deletedAt IS NULL')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findActiveProjects()
    {
        return $this->createQueryBuilder('p')
                    ->andWhere('p.deletedAt IS NULL')
                    ->orderBy('p.id', "DESC")
                    ->getQuery()->getResult();
    }
}
