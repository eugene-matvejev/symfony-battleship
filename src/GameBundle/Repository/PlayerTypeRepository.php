<?php

namespace GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use GameBundle\Entity\PlayerType;
use GameBundle\Model\PlayerModel;

/**
 * @since 1.0
 */
class PlayerTypeRepository extends EntityRepository
{
    /**
     * @return PlayerType[]
     */
    public function getTypes() : array
    {
        return $this
            ->createQueryBuilder('q', 'q.id')
            ->where((new Expr())->in('q.id', PlayerModel::getAllTypes()))
            ->getQuery()
            ->getResult();
    }
}