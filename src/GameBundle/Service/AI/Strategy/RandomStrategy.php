<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;

/**
 * @since 3.5
 */
class RandomStrategy extends AbstractStrategy
{
    public function __construct(CellModel $cellModel)
    {
        $this->cellModel = $cellModel;
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    public function verify(Cell $cell) : array
    {
        return $this->verifyByCoordinates([
            new CoordinatesPair(CoordinatesPair::WAY_LEFT, $cell->getX() + 1, $cell->getY()), // -- left
            new CoordinatesPair(CoordinatesPair::WAY_RIGHT, $cell->getX() - 1, $cell->getY()),// ++ right
            new CoordinatesPair(CoordinatesPair::WAY_UP, $cell->getX(), $cell->getY() + 1),   // -- up
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, $cell->getX(), $cell->getY() - 1)  // ++ down
        ]);
    }
}
