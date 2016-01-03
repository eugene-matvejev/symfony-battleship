<?php

namespace GameBundle\Repository;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Game;
use GameBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * BattlefieldRepository
 */
class BattlefieldRepository extends EntityRepository
{
    /**
     * @param int $gameId
     *
     * @return Battlefield[]
     */
    public function findByGameId($gameId)
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'g')
            ->join('b.game', 'g')
            ->where('g.id = :game')->setParameter('game', $gameId)
            ->getQuery()->getResult();
    }

    /**
     * @param Game $game
     *
     * @return Battlefield[]
     */
    public function findNotCPUsByGame(Game $game)
    {
        return $this->createQueryBuilder('b')
            ->select('b', 'p')
            ->join('b.player', 'p')
            ->where('b.game = :game')->setParameter('game', $game)
            ->andWhere('p.type != :type')->setParameter('type', PlayerModel::TYPE_CPU)
            ->getQuery()->getResult();
    }
}