<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Game;
use Doctrine\ORM\EntityRepository;

/**
 * Class GameRepository
 * @package AppBundle\Repository
 */
class GameRepository extends EntityRepository
{
    /**
     * @param int $id
     *
     * @return Game
     */
    public function findById($id) {
        return $this->findOneBy(['id' => $id]);
    }
}