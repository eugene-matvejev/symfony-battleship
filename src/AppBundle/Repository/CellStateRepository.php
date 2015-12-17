<?php

namespace AppBundle\Repository;

use AppBundle\Entity\CellState;
use AppBundle\Model\CellModel;
use Doctrine\ORM\EntityRepository;

/**
 * CellStateRepository
 * @package AppBundle\Repository
 */
class CellStateRepository extends EntityRepository
{
    /**
     * @return CellState[]
     */
    public function getStates()
    {
        $arr = [];
        foreach($this->findBy(['id' => CellModel::getAllStates()]) as $state) {
            /** @var CellState $state */
            $arr[$state->getId()] = $state;
        }

        return $arr;
    }
}