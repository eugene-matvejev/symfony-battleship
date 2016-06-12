<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 12.0
 */
class CellModel
{
    const FLAG_NONE      = 0x00;
    const FLAG_DEAD      = 0x01;
    const FLAG_SHIP      = 0x02;
    const FLAG_SKIP      = 0x04 | self::FLAG_DEAD;
    const FLAG_DEAD_SHIP = self::FLAG_SHIP | self::FLAG_DEAD;
    /**
     * @var Cell[]
     */
    protected static $changedCells = [];

    /**
     * @return Cell[]
     */
    public static function getChangedCells() : array
    {
        return static::$changedCells;
    }

    /**
     * @param Cell $cell
     * @param int  $additionalFlag - additional flag which will be applied with CellModel::FLAG_DEAD
     *
     * @return Cell
     */
    public static function switchPhase(Cell $cell, int $additionalFlag = self::FLAG_NONE) : Cell
    {
        if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
            static::$changedCells[$cell->getId()] = $cell->addFlag($additionalFlag | CellModel::FLAG_DEAD);
        }

        return $cell;
    }

    public static function isShipDead(Cell $cell) : bool
    {
        if (!$cell->hasFlag(static::FLAG_DEAD_SHIP)) {
            return false;
        }

        foreach ((new PathProcessor($cell->getCoordinate()))->getAdjacentCells($cell->getBattlefield(), 4, static::FLAG_SHIP) as $cell) {
            if (!$cell->hasFlag(static::FLAG_DEAD_SHIP)) {
                return false;
            }
        }

        return true;
    }
}
