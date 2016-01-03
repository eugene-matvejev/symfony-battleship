<?php

namespace GameBundle\Repository;

use GameBundle\Entity\PlayerType;
use GameBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * PlayerTypeRepository
 */
class PlayerTypeRepository extends EntityRepository
{
    /**
     * @return PlayerType[]
     */
    public function getTypes()
    {
        $arr = [];
        foreach($this->findBy(['id' => PlayerModel::getAllTypes()]) as $type) {
            /** @var PlayerType $type */
            $arr[$type->getId()] = $type;
        }

        return $arr;
    }
}