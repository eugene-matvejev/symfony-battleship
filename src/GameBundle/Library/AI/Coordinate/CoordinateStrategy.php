<?php

namespace GameBundle\Library\AI\Coordinate;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;
use GameBundle\Model\BattlefieldModel;
use GameBundle\Model\CellModel;

class CoordinateStrategy
{
    const STRATEGY_X = 0;
    const STRATEGY_Y = 1;
    const STRATEGY_Z = 2;
    /**
     * @var int
     */
    private $minShipSize;
    /**
     * @var int
     */
    private $maxShipSize;
    /**
     * @var int[]
     */
    private $checkedCells;

    /**
     * @param int $min
     * @param int $max
     */
    public function __construct(\int $min, \int $max)
    {
        $this->minShipSize = $min;
        $this->maxShipSize = $max;
        $this->checkedCells = [];
    }

    /**
     * @param Battlefield $battlefield
     * @return Cell[]
     */
    public function findPair(Battlefield $battlefield) : array
    {
        $pairs = [];
        foreach($this->chooseStrategy($battlefield) as $el) {
            $pairs[] = $el;
        }

        return $pairs;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return mixed
     */
    private function chooseStrategy(Battlefield $battlefield)
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || isset($this->checkedCells[$cell->getId()])) {
                continue;
            }

            $this->checkedCells[$cell->getId()] = $cell;

            if($this->isCellContainsDeadShip($cell->getBattlefield(), $cell->getX() + 1, $cell->getY())) {
                $pairs = $this->xStrategy($cell);
                if(null !== $pairs->{0} || null !== $pairs->{1}) {
                    return $pairs;
                }
            }
            if($this->isCellContainsDeadShip($cell->getBattlefield(), $cell->getX(), $cell->getY() + 1)) {
                $pairs = $this->yStrategy($cell);
                if(null !== $pairs->{0} || null !== $pairs->{1}) {
                    return $pairs;
                }
            }

            $pairs = $this->zStrategy($cell);
            if(null !== $pairs->{0} || null !== $pairs->{1}) {
                return $pairs;
            }
        }
        return [];
    }

    /**
     * @param Battlefield $battlefield
     * @param int $x
     * @param int $y
     *
     * @return bool
     */
    private function isCellContainsDeadShip(Battlefield $battlefield, \int $x, \int $y)
    {
        $cell = BattlefieldModel::getCellByCoordinates($battlefield, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }


    private function xStrategy(Cell $cell)
    {
        $pairs = new \stdClass();
        $pairs->{0} = $cell->getX() - 1;
        $pairs->{1} = $cell->getX() + 1;

        for($i = 0; $i < $this->maxShipSize; $i++) {
            foreach($pairs as $index => $pair) {
                if(null === $pair || $pair instanceof CoordinatePair) {
                    continue;
                }

                $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $pair, $cell->getY());
                if(null !== $_cell) {
                    if($_cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                        $pairs->{$index} = $_cell->getState()->getId() === CellModel::STATE_WATER_DIED
                            ? null
                            : new CoordinatePair($_cell->getX(), $_cell->getY());
                    }
                }

                if(is_numeric($pairs->{$index})) {
                    $pairs->{$index} % 2 === 0
                        ? $pairs->{$index}++
                        : $pairs->{$index}--;
                }
            }
        }

        $pairs->{0} = is_numeric($pairs->{0}) ? $pairs->{0} : null;
        $pairs->{1} = is_numeric($pairs->{1}) ? $pairs->{1} : null;

        return $pairs;
    }

    private function yStrategy(Cell $cell)
    {
        $pairs = new \stdClass();
        $pairs->{0} = $cell->getY() - 1;
        $pairs->{1} = $cell->getY() + 1;

        return $pairs;
    }

    /**
     * @param Cell $cell
     *
     * @return Cell[]
     */
    private function zStrategy(Cell $cell) : array
    {
        $cells = [];
        if(null !== $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $cell->getX() + 1, $cell->getY()))
            $cells[] = $_cell;
        if(null !== $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $cell->getX() - 1, $cell->getY()))
            $cells[] = $_cell;
        if(null !== $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $cell->getX(), $cell->getY() + 1))
            $cells[] = $_cell;
        if(null !== $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $cell->getX(), $cell->getY() - 1))
            $cells[] = $_cell;

        return $cells;
    }

    private function isShipDead(Cell $cell, \int $strategy)
    {
        $x = $cell->getX();
        $y = $cell->getY();
        $cell1 = null;
        $cell2 = null;

        switch($strategy) {
            case self::STRATEGY_X:
                $x1 = $x - 1;
                $x2 = $x + 1;

                for($i = 0; $i < $this->maxShipSize; $i++) {
                    if(null === $cell1) {
                        $cell1 = $this->verifyCell($cell, $x1, $cell->getY());
                    }
                    if(null === $cell2) {
                        $cell2 = $this->verifyCell($cell, $x2, $cell->getY());
                    }
                    $x1--; $x2++;
                }
                break;
            case self::STRATEGY_Y:
                $y1 = $y - 1;
                $y2 = $y + 1;

                for($i = 0; $i < $this->maxShipSize; $i++) {
                    if(null === $cell1) {
                        $cell1 = $this->verifyCell($cell, $cell->getX(), $y1);
                    }
                    if(null === $cell2) {
                        $cell2 = $this->verifyCell($cell, $cell->getX(), $y2);
                    }
                    $y1--; $y2++;
                }
                break;
            case self::STRATEGY_Z:
                break;
        }
    }

    private function verifyCell(Cell $cell, \int $x, \int $y)
    {
        $_cell = BattlefieldModel::getCellByCoordinates($cell->getBattlefield(), $x, $y);
        if(null !== $_cell) {
            if($_cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                return $_cell->getState()->getId() !== CellModel::STATE_WATER_DIED ? $_cell : false;
            }

            return null;
        }

        return false;
    }
}