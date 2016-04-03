<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\CellState;

/**
 * @since 7.0
 */
trait CellStateMockTrait
{
    protected function getCellStateMock(int $stateId) : CellState
    {
        return (new CellState())
            ->setId($stateId);
    }
}
