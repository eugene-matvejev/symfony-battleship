<?php

namespace AppBundle\Repository;

use AppBundle\Model\CellStateModel;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class CellStateRepository
 * @package AppBundle\Repository
 */
class CellStateRepository extends EntityRepository {
    public function getStates() {
        return [
            CellStateModel::WATER_LIVE => $this->findOneBy(['id' => CellStateModel::WATER_LIVE]),
            CellStateModel::WATER_DIED => $this->findOneBy(['id' => CellStateModel::WATER_DIED]),
            CellStateModel::SHIP_LIVE  => $this->findOneBy(['id' => CellStateModel::SHIP_LIVE]),
            CellStateModel::SHIP_LIVE  => $this->findOneBy(['id' => CellStateModel::SHIP_LIVE])
        ];
    }
}