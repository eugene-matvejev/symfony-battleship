<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Battlefield;
use AppBundle\Entity\Game;
use AppBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * Class BattlefieldRepository
 * @package AppBundle\Repository
 */
class BattlefieldRepository extends EntityRepository {
    /**
     * @param Game $game
     *
     * @return Battlefield[]
     */
    public function findByGame(Game $game)
    {
        return $this->findOneBy(['game' => $game]);
    }

    /**
     * @param Game $game
     *
     * @return Battlefield[]
     */
    public function findNotCPUsByGame(Game $game)
    {
        return $this->createQueryBuilder('b')
//            ->select('b', 'p', 'pt')
            ->select('b', 'p')
            ->join('b.player', 'p')
//            ->join('p.type', 'pt')
            ->where('b.game = :game')->setParameter('game', $game)
            ->andWhere('p.type != :type')->setParameter('type', PlayerModel::TYPE_CPU)
            ->getQuery()->getResult();
    }
}