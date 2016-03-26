<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\CellModel;

/**
 * @since 7.0
 */
trait CellStateMockTrait
{
    protected function getCellStateMock(int $stateId = null) : CellState
    {
        return (new CellState())
            ->setId($stateId ?? CellModel::STATE_WATER_LIVE);
    }
}
