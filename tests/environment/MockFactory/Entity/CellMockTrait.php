<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;

/**
 * @since 7.0
 */
trait CellMockTrait
{
    protected function getCellMock(string $coordinate, int $mask = CellModel::FLAG_NONE) : Cell
    {
        return (new Cell())
            ->setCoordinate($coordinate)
            ->setFlag($mask);
    }
}
