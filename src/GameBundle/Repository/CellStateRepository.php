<?php

namespace GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use GameBundle\Entity\CellState;
use GameBundle\Model\CellModel;

/**
 * CellStateRepository
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
            ->where((new Expr())->in('q.id', CellModel::getAllStates()))
            ->getQuery()
            ->getResult();
    }
}