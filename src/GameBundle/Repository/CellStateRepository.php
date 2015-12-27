<?php

namespace GameBundle\Repository;

use GameBundle\Entity\CellState;
use GameBundle\Model\CellModel;
use Doctrine\ORM\EntityRepository;

/**
 * CellStateRepository
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