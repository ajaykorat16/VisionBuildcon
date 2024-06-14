<?php

namespace App\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

trait BaseRepository
{
    protected function initPaginator(QueryBuilder $qb, bool $fetchJoinCollection = true): Paginator
    {
        return new Paginator($qb, $fetchJoinCollection);
    }

    protected function paginateResults(QueryBuilder $qb, bool $fetchJoinCollection = true, bool $useOutputWalkers = true): ArrayCollection
    {
        $paginator = $this->initPaginator($qb, $fetchJoinCollection);
        if (!$useOutputWalkers) {
            $paginator->setUseOutputWalkers(false);
        }
        $results = new ArrayCollection();

        foreach ($paginator as $result) {
            $results->add($result);
        }

        return $results;
    }
}