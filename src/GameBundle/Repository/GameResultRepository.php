<?php

namespace GameBundle\Repository;

use GameBundle\Entity\GameResult;
use Doctrine\ORM\EntityRepository;

/**
 * @since 1.0
 */
class GameResultRepository extends EntityRepository
{
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return GameResult[]
     */
    public function getAllOrderByDate(int $page, int $perPage) : array
    {
        return $this->findBy([], ['timestamp' => 'DESC'], $perPage, ($page < 1 ? 0 : $page - 1) * $perPage);
    }

    /**
     * @return int
     */
    public function countTotalResults() : int
    {
        return $this
            ->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}