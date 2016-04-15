<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;

/**
 * @since 7.0
 */
trait CellStateMockTrait
{
    protected function getCellStateMock(int $stateId = CellModel::STATE_WATER_LIVE) : CellState
    {
        return (new CellState())
            ->setId($stateId);
    }

    protected function getLiveShipCellStateMock() : CellState
    {
        return $this->getCellStateMock(CellModel::STATE_SHIP_LIVE);
    }

    protected function getDeadShipCellStateMock() : CellState
    {
        return $this->getCellStateMock(CellModel::STATE_SHIP_DIED);
    }

    protected function getDeadWaterCellStateMock() : CellState
    {
        return $this->getCellStateMock(CellModel::STATE_WATER_DIED);
    }
}
