<?php

namespace AppBundle\Repository;

use AppBundle\Entity\GameResult;
use Doctrine\ORM\EntityRepository;

/**
 * GameResultRepository
 * @package AppBundle\Repository
 */
class GameResultRepository extends EntityRepository
{
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return GameResult[]
     */
    public function getResultsInDescendingDate(\int $page, \int $perPage)
    {
        return $this->findBy([], ['timestamp' => 'DESC'], $perPage, ($page < 1 ? 0 : $page - 1) * $perPage);
    }
}