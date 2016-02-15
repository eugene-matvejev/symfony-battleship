<?php

namespace EM\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;

abstract class AbstractStrategy
{
    /**
     * @var CellModel
     */
    protected $cellModel;

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    public function verify(Cell $cell) : array {}

    /**
     * @param CoordinatesPair[] $coordinatesPairs
     * @param bool|false        $closestOnly
     *
     * @return Cell[]
     */
    protected function verifyByCoordinates(array $coordinatesPairs, bool $closestOnly = false) : array
    {
        $cells = [];

        foreach ($coordinatesPairs as $coordinatesPair) {
            while (null !== $cell = $this->cellModel->getByCoordinatesPair($coordinatesPair)) {
                if (!in_array($cell->getState()->getId(), CellModel::STATES_LIVE)) {
                    break;
                }

                $cells[] = $cell;
                if ($closestOnly) {
                    break;
                }
                $coordinatesPair->prepareForNextStep();
            }
        }

        return $cells;
    }
}