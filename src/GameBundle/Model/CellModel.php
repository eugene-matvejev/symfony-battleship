<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 12.0
 */
class CellModel
{
    const FLAG_NONE = 0x00;
    const FLAG_DEAD = 0x01;
    const FLAG_SHIP = 0x02;
    const FLAG_SKIP = 0x04 | self::FLAG_DEAD;
    const FLAG_DEAD_SHIP = self::FLAG_SHIP | self::FLAG_DEAD;
    /**
     * @var Cell[]
     */
    private static $changedCells = [];
    /**
     * @var Cell[]
     */
    private static $checkedCells = [];

    /**
     * @return Cell[]
     */
    public static function getChangedCells() : array
    {
        return self::$changedCells;
    }

    public static function switchPhase(Cell $cell, int $customMask = null) : Cell
    {
        if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
            self::$changedCells[$cell->getId()] = $cell->addFlag($customMask ?? CellModel::FLAG_DEAD);
        }

        return $cell;
    }

    public static function isShipDead(Cell $cell) : bool
    {
        if (isset(self::$checkedCells[$cell->getId()])) {
            return true;
        }

        if (!$cell->hasFlag(self::FLAG_DEAD_SHIP)) {
            return false;
        }

        $PathProcessor = new PathProcessor($cell);
        $battlefield = $cell->getBattlefield();
        $cells = [$cell->getCoordinate() => $cell];

        foreach (PathProcessor::PRIMARY_PATHS as $way) {
            $PathProcessor->setPath($way);

            /** @var Cell $cell */
            while (null !== $cell = $battlefield->getCellByCoordinate($PathProcessor->getNextCoordinate())) {
                if (isset($cells[$cell->getCoordinate()]) || !$cell->hasFlag(self::FLAG_SHIP)) {
                    break;
                }
                if (!$cell->hasFlag(self::FLAG_DEAD)) {
                    return false;
                }

                $cells[$cell->getCoordinate()] = $cell;
            }
        }

        foreach ($cells as $cell) {
            self::$checkedCells[$cell->getId()] = $cell;

            foreach ((new PathProcessor($cell))->getAdjacentCells() as $_cell) {
                self::switchPhase($_cell, self::FLAG_SKIP);
            }
        }

        return true;
    }
}
