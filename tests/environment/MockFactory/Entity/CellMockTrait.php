<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;

/**
 * @since 7.0
 */
trait CellMockTrait
{
    use CellStateMockTrait;

    protected function getCellMock(string $coordinate, int $stateId = CellModel::STATE_WATER_LIVE) : Cell
    {
        return (new Cell())
            ->setCoordinate($coordinate)
            ->setState($this->getCellStateMock($stateId));
    }
}
