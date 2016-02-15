<?php

namespace EM\GameBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Game;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 1.0
 */
class BattlefieldRepository extends EntityRepository
{
    /**
     * @param int $gameId
     *
     * @return Battlefield[]
     */
    public function findByGameId(int $gameId) : array
    {
        return $this
            ->createQueryBuilder('b')
            ->select('b', 'g')
            ->join('b.game', 'g')
            ->where((new Expr())->eq('g.id', $gameId))
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Game $game
     *
     * @return Battlefield[]
     */
    public function findNotCPUsByGame(Game $game) : array
    {
        return $this
            ->createQueryBuilder('b')
            ->select('b', 'p')
            ->join('b.player', 'p')
            ->where((new Expr())->eq('b.game', $game))
            ->andWhere((new Expr())->neq('p.type', PlayerModel::TYPE_CPU))
            ->getQuery()
            ->getResult();
    }
}
