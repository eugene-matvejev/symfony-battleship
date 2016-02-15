<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;

/**
 * @since 3.5
 */
class YStrategy extends AbstractStrategy
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
        return parent::verifyByCoordinates([
            new CoordinatesPair(CoordinatesPair::WAY_UP, $cell->getX(), $cell->getY() + 1),   // -- up
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, $cell->getX(), $cell->getY() - 1)  // ++ down
        ]);
    }
}
