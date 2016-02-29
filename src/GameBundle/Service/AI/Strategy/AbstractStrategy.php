<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;

/**
 * @since 3.5
 */
abstract class AbstractStrategy
{
    /**
     * @var CellModel
     */
    protected $cellModel;

    public function getCellModel() : CellModel
    {
        return $this->cellModel;
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    abstract function verify(Cell $cell) : array;

    /**
     * @param CoordinatesPair[] $coordinatesPairs
     *
     * @return Cell[]
     */
    protected function verifyByCoordinates(array $coordinatesPairs) : array
    {
        $cells = [];

        foreach ($coordinatesPairs as $coordinatesPair) {
            if (null !== $cell = $this->cellModel->getByCoordinatesPair($coordinatesPair)) {
                if (in_array($cell->getState()->getId(), CellModel::STATES_LIVE)) {
                    $cells[] = $cell;
                }
            }
        }

        return $cells;
    }
}
