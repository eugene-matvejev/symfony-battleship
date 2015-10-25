<?php

namespace AppBundle\Repository;

use AppBundle\Entity\PlayerType;
use AppBundle\Model\PlayerModel;
use Doctrine\ORM\EntityRepository;

/**
 * Class PlayerTypeRepository
 * @package AppBundle\Repository
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
            /**
             * @var $type PlayerType
             */
            $arr[$type->getId()] = $type;
        }

        return $arr;
    }
}