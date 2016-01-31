<?php

namespace EM\GameBundle\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;

/**
 * @since 3.0
 */
class AIStrategy
{
    const COORDINATES_STEPS = [-1, 0, 1];
    /**
     * @var int
     */
    private $minShipSize;
    /**
     * @var int
     */
    private $maxShipSize;
    /**
     * @var CellModel
     */
    private $cellModel;

    public function __construct(int $min, int $max, CellModel $model)
    {
        $this->minShipSize = $min;
        $this->maxShipSize = $max;
        $this->cellModel = $model;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public function chooseStrategy(Battlefield $battlefield) : array
    {
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
        $coordinates = [
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()]
        ];

        return $this->strategy($cell->getBattlefield(), $coordinates, 'x');
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function yStrategy(Cell $cell) : array
    {
        $coordinates = [
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1]
        ];

        return $this->strategy($cell->getBattlefield(), $coordinates, 'y');
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function zStrategy(Cell $cell) : array
    {
        $cells = [];
        $coordinates = [
            ['x' => $cell->getX() + 1, 'y' => $cell->getY()],
            ['x' => $cell->getX() - 1, 'y' => $cell->getY()],
            ['x' => $cell->getX(), 'y' => $cell->getY() + 1],
            ['x' => $cell->getX(), 'y' => $cell->getY() - 1],
        ];

        foreach ($coordinates as $coordinate) {
            if (null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinate['x'], $coordinate['y'])) {

                if (in_array($_cell->getState()->getId(), CellModel::getLiveStates())) {
                    $cells[] = $_cell;
                }
            }
        }

        return $cells;
    }

    /**
     * @param Battlefield $battlefield
     * @param array       $coordinates
     * @param string      $axis
     *
     * @return Cell[]
     */
    private function strategy(Battlefield $battlefield, array $coordinates, string $axis) : array
    {
        $cells = [];

        for ($i = 0; $i < $this->maxShipSize; $i++) {
            foreach ($coordinates as $i => $coordinate) {
                if (null !== $cell = CellModel::getByCoordinates($battlefield, $coordinate['x'], $coordinate['y'])) {

                    if (in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                        $cells[$i] = $cell;
                        unset($coordinates[$i]);
                    } elseif (0 === $i) {
                        $coordinates[$i][$axis]++;
                    } elseif (1 === $i) {
                        $coordinates[$i][$axis]--;
                    }
                } else {
                    unset($coordinates[$i]);
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
            'left'  => (object)['x' => $cell->getX() - 1, 'y' => $cell->getY()], // -- left
            'right' => (object)['x' => $cell->getX() + 1, 'y' => $cell->getY()], // ++ right
            'up'    => (object)['x' => $cell->getX(), 'y' => $cell->getY() - 1], // -- up
            'down'  => (object)['x' => $cell->getX(), 'y' => $cell->getY() + 1]  // ++ down
        ];

        foreach ($coordinates as $side => $coordinate) {
            for ($i = 0; $i < $this->maxShipSize; $i++) {
                if (null !== $_cell = CellModel::getByCoordinates($cell->getBattlefield(), $coordinate->x, $coordinate->y)) {
                    if (in_array($_cell->getState()->getId(), CellModel::getShipStates())) {
                        if ($_cell->getState()->getId() === CellModel::STATE_SHIP_DIED) {
                            switch ($side) {
                                case 'left':  $coordinate->x--; break;
                                case 'right': $coordinate->x++; break;
                                case 'up':    $coordinate->y--; break;
                                case 'down':  $coordinate->y++; break;
                            }
                            $cells[] = $_cell;
                        } else {
                            $cells = [];
                            break;
                        }
                    } else {
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        if (!empty($cells)) {
            /**
             * @var Cell $_cell
             *
             *  x-1; y-1 | x ; y-1 | x+1; y-1
             *  x-1;   y | x ; y   | x+1; y
             *  x-1; y+1 | x ; y+1 | x+1; y+1
             */
            $cells[] = $cell;

            foreach ($cells as $_cell) {
                for ($x = 0; $x < 3; $x++) {
                    for ($y = 0; $y < 3; $y++) {
                        $_x = $_cell->getX() + self::COORDINATES_STEPS[$x];
                        $_y = $_cell->getY() + self::COORDINATES_STEPS[$y];

                        if (null !== $__cell = CellModel::getByCoordinates($cell->getBattlefield(), $_x, $_y)) {
                            $this->cellModel->switchStateToSkipped($__cell);
                        }
                    }
                }
            }

            return true;
        }

        return false;
    }
}