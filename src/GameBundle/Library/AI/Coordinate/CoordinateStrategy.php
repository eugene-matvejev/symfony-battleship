<?php

namespace GameBundle\Library\AI;

use GameBundle\Entity\Battlefield;
use GameBundle\Model\BattlefieldModel;

class Strategy
{
    const STRATEGY_X = 0;
    const STRATEGY_Y = 1;
    const STRATEGY_Z = 2; /* random */


    /**
     * @param Battlefield $battlefield
     * @param int         $x
     * @param int         $y
     * @return bool
     */
    private function analyzeCell(Battlefield $battlefield, \int $x, \int $y)
    {
        $cell = BattlefieldModel::getCellByCoordinates($battlefield, $x, $y);

        return null !== $cell && $cell->getState()->getId() === CellModel::STATE_SHIP_DIED;
    }

    private function getCoordinatesPair()
    {

    }

    /**
     * @param Cell[] $cells
     */
    private function chooseStrategy(array $cells)
    {
        foreach($cells as $cell) {
            if($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED) {
                continue;
            }

            if($this->analyzeCell($cell->getBattlefield(), $cell->getX() + 1, $cell->getY()))
                return self::STRATEGY_X;
            if($this->analyzeCell($cell->getBattlefield(), $cell->getX(), $cell->getY() + 1))
                return self::STRATEGY_Y;
        }

        return self::STRATEGY_Z;
    }

    private function zStrategy()
    {

    }

    private function xStrategy()
    {

    }

    private function yStrategy()
    {

    }

    private function isShipDead(Cell $cell, \int $strategy)
    {

    }
}