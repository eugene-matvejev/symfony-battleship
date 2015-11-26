<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Game;
use Doctrine\ORM\EntityRepository;

/**
 * Class GameRepository
 * @package AppBundle\Repository
 *
 * @method Game find($id)
 */
class GameRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Game
     */
    public function findById(\int $id)
    {
        return $this->findOneBy(['id' => $id]);
    }
}