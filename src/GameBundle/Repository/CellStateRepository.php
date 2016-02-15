<?php

namespace EM\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;

/**
 * @since 1.0
 */
class CellStateRepository extends EntityRepository
{
    /**
     * @return CellState[]
     */
    public function getStates() : array
    {
        return $this
            ->createQueryBuilder('q', 'q.id')
            ->where((new Expr())->in('q.id', CellModel::STATES_ALL))
            ->getQuery()
            ->getResult();
    }
}
