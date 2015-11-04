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
    public function findById($id)
    {
//        $this->
        return $this->findOneBy(['id' => $id]);
    }
}