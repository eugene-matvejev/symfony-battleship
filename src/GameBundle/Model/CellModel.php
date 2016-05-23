<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\CellException;
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

    /**
     * @param Cell $cell
     * @param int  $additionalFlag - additional flag which will be applied with CellModel::FLAG_DEAD
     *
     * @return Cell
     */
    public static function switchPhase(Cell $cell, int $additionalFlag = self::FLAG_NONE) : Cell
    {
        if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
            self::$changedCells[$cell->getId()] = $cell->addFlag($additionalFlag | CellModel::FLAG_DEAD);
        }

        return $cell;
    }

    /**
     * @param Cell $cell
     * @param int  $requiredFlag if
     *
     * @return Cell[]
     * @throws CellException
     */
    public static function getShipCells(Cell $cell, int $requiredFlag = self::FLAG_NONE) : array
    {
        $battlefield = $cell->getBattlefield();
        $processor = new PathProcessor($cell);

        $cells = [$cell->getCoordinate() => $cell];

        foreach (PathProcessor::PRIMARY_PATHS as $way) {
            $processor->setPath($way);

            /** @var Cell $cell */
            while (null !== $cell = $battlefield->getCellByCoordinate($processor->getNextCoordinate())) {
                if (isset($cells[$cell->getCoordinate()]) || !$cell->hasFlag(self::FLAG_SHIP)) {
                    break;
                }
                if (!$cell->hasFlag($requiredFlag)) {
                    throw new CellException("ship cell: {$cell->getId()} missed required flag: {$requiredFlag}");
                }

                $cells[$cell->getCoordinate()] = $cell;
            }
        }

        return $cells;
    }

    public static function isShipDead(Cell $cell) : bool
    {
        if (isset(self::$checkedCells[$cell->getId()])) {
            return true;
        }

        if (!$cell->hasFlag(self::FLAG_DEAD_SHIP)) {
            return false;
        }

        try {
            foreach (static::getShipCells($cell, self::FLAG_DEAD) as $shipCell) {
                self::$checkedCells[$cell->getId()] = $shipCell;

                foreach ((new PathProcessor($shipCell))->getAdjacentCells(CellModel::FLAG_SHIP) as $waterCell) {
                    self::switchPhase($waterCell, static::FLAG_SKIP);
                }
            }

            return true;
        } catch (CellException $e) {
            return false;
        }
    }
}
