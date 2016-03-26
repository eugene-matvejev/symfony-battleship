<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Entity\Cell;

/**
 * @since 7.0
 */
trait CellMockTrait
{
    use CellStateMockTrait;

    private function getCellMock(string $coordinate, int $state = null) : Cell
    {
        return (new Cell())
            ->setCoordinate($coordinate)
            ->setState($this->getCellStateMock($state));
    }
}
