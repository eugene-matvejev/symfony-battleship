<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\Game;
use AppBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * BattlefieldRepository
 * @package AppBundle\Repository
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