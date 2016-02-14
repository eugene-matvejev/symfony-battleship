<?php

namespace EM\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\CoordinateSystem\CoordinatesPair;

/**
 * @since 3.0
 */
class AIStrategyService
{
    const COORDINATES_STEPS = [-1, 0, 1];
    /**
     * @var CellModel
     */
    private $cellModel;

    public function __construct(CellModel $model)
    {
        $this->cellModel = $model;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public function chooseStrategy(Battlefield $battlefield) : array
    {
        $this->cellModel->indexCells($battlefield);

        foreach ($battlefield->getCells() as $cell) {
            if ($cell->getState()->getId() === CellModel::STATE_SHIP_DIED && !$this->isShipDead($cell)) {
                // x Strategy
                $cells = $this->xStrategy($cell);
                // y Strategy
                $cells = empty($cells) ? $this->yStrategy($cell) : $cells;
                // z Strategy
                $cells = empty($cells) ? $this->zStrategy($cell) : $cells;

                if (!empty($cells)) {
                    return $cells;
                }
            }
        }

        return [];
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function xStrategy(Cell $cell) : array
    {
        return $this->strategy([
            new CoordinatesPair(CoordinatesPair::WAY_LEFT, $cell->getX() + 1, $cell->getY()), // -- left
            new CoordinatesPair(CoordinatesPair::WAY_RIGHT, $cell->getX() - 1, $cell->getY()) // ++ right
        ]);
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function yStrategy(Cell $cell) : array
    {
        return $this->strategy([
            new CoordinatesPair(CoordinatesPair::WAY_UP, $cell->getX(), $cell->getY() + 1),   // -- up
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, $cell->getX(), $cell->getY() - 1)  // ++ down
        ]);
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function zStrategy(Cell $cell) : array
    {
        return $this->strategy([
            new CoordinatesPair(CoordinatesPair::WAY_LEFT, $cell->getX() + 1, $cell->getY()), // -- left
            new CoordinatesPair(CoordinatesPair::WAY_RIGHT, $cell->getX() - 1, $cell->getY()),// ++ right
            new CoordinatesPair(CoordinatesPair::WAY_UP, $cell->getX(), $cell->getY() + 1),   // -- up
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, $cell->getX(), $cell->getY() - 1)  // ++ down
        ], true);
    }

    /**
     * @param CoordinatesPair[] $coordinates
     * @param bool|false       $closestOnly
     *
     * @return Cell[]
     */
    private function strategy(array $coordinates, bool $closestOnly = false) : array
    {
        $cells = [];

        foreach ($coordinates as $CoordinatesPair) {
            while (null !== $cell = $this->cellModel->getByCoordinatesPair($CoordinatesPair)) {
                if (in_array($cell->getState()->getId(), CellModel::STATES_LIVE)) {
                    $cells[] = $cell;
                    if ($closestOnly) {
                        break;
                    } else {
                        $CoordinatesPair->prepareForNextStep();
                    }
                } else {
                    break;
                }
            }
        }

        return $cells;
    }

    public function isShipDead(Cell $cell) : bool
    {
        if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
            return false;
        }

        $coordinates = [
            new CoordinatesPair(CoordinatesPair::WAY_LEFT, $cell->getX() + 1, $cell->getY()), // -- left
            new CoordinatesPair(CoordinatesPair::WAY_RIGHT, $cell->getX() - 1, $cell->getY()),// ++ right
            new CoordinatesPair(CoordinatesPair::WAY_UP, $cell->getX(), $cell->getY() + 1),   // -- up
            new CoordinatesPair(CoordinatesPair::WAY_DOWN, $cell->getX(), $cell->getY() - 1)  // ++ down
        ];
        $cells = [$cell];

        /**
         * @var CoordinatesPair[] $coordinates
         */
        foreach ($coordinates as $coordinate) {
            while (null !== $_cell = $this->cellModel->getByCoordinatesPair($coordinate)) {
                if (in_array($_cell->getState()->getId(), CellModel::STATES_SHIP)) {
                    if ($_cell->getState()->getId() === CellModel::STATE_SHIP_DIED) {
                        $coordinate->prepareForNextStep();
                        $cells[] = $_cell;
                    } else {
                        return false;
                    }
                } else {
                    break;
                }
            }
        }

        /**
         * @var Cell[] $cells
         *
         *  x-1; y-1 | x ; y-1 | x+1; y-1
         *  x-1;   y | x ; y   | x+1; y
         *  x-1; y+1 | x ; y+1 | x+1; y+1
         */
        foreach ($cells as $cell) {
            foreach (self::COORDINATES_STEPS as $x) {
                foreach (self::COORDINATES_STEPS as $y) {
                    if (null !== $_cell = $this->cellModel->getByCoordinates($cell->getX() + $x, $cell->getY() + $y)) {
                        $this->cellModel->switchStateToSkipped($_cell);
                    }
                }
            }
        }

        return true;
    }
}