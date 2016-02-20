<?php

namespace EM\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 1.0
 */
class PlayerTypeRepository extends EntityRepository
{
    /**
     * @return PlayerType[]
     */
    public function getAllIndexed() : array
    {
        return $this
            ->createQueryBuilder('q', 'q.id')
            ->where((new Expr())->in('q.id', PlayerModel::TYPES_ALL))
            ->getQuery()
            ->getResult();
    }
}
