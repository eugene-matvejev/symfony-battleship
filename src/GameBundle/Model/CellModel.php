<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 12.0
 */
class CellModel
{
    const MASK_NONE = 0x0000;
    const MASK_DEAD = 0x0001;
    const MASK_SHIP = 0x0002;
    const MASK_SKIP = 0x0004 | self::MASK_DEAD;
    const MASK_DEAD_SHIP = self::MASK_SHIP | self::MASK_DEAD;

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

    public function switchState(Cell $cell, int $customMask = null) : Cell
    {
        if (!$cell->hasMask(CellModel::MASK_DEAD)) {
            self::$changedCells[$cell->getId()] = $cell->addMask($customMask ?? CellModel::MASK_DEAD);
        }

        return $cell;
    }

    public function switchStateToSkipped(Cell $cell) : Cell
    {
        return $this->switchState($cell, self::MASK_SKIP);
    }

    public function isShipDead(Cell $cell) : bool
    {
        if (!$cell->hasMask(self::MASK_DEAD_SHIP)) {
            return false;
        }
        if (isset(self::$checkedCells[$cell->getId()])) {
            return true;
        }

        $PathProcessor = new PathProcessor($cell);
        $cells = [$cell->getCoordinate() => $cell];

        foreach (PathProcessor::PRIMARY_PATHS as $way) {
            $PathProcessor->setPath($way);

            while (null !== $_cell = $cell->getBattlefield()->getCellByCoordinate($PathProcessor->getNextCoordinate())) {
                if (!$_cell->hasMask(self::MASK_SHIP)) {
                    break;
                }
                if (!$_cell->hasMask(self::MASK_DEAD)) {
                    return false;
                }

                $cells[$_cell->getCoordinate()] = $_cell;
            }
        }

        foreach ($cells as $cell) {
            self::$checkedCells[$cell->getId()] = $cell;

            foreach ((new PathProcessor($cell))->getAdjacentCells() as $_cell) {
                $this->switchStateToSkipped($_cell);
            }
        }

        return true;
    }
}
