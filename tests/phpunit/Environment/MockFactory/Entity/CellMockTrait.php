<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Cell;

/**
 * @since 7.0
 */
trait CellMockTrait
{
    use CellStateMockTrait;

    protected function getCellMock(string $coordinate, int $state = null) : Cell
    {
        return (new Cell())
            ->setCoordinate($coordinate)
            ->setState($this->getCellStateMock($state));
    }
}
