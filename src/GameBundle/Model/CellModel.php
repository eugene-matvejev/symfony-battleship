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
    protected static $changedCells = [];
    /**
     * @var Cell[]
     */
    protected static $checkedCells = [];

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
        $processor = new PathProcessor($cell->getCoordinate());

        $cells = [$cell->getCoordinate() => $cell];

        foreach (PathProcessor::PRIMARY_PATHS as $path) {
            $processor->setPath($path);

            /** @var Cell $cell */
            while (null !== $cell = $battlefield->getCellByCoordinate($processor->getNextCurrentCoordinate())) {
                if (isset($cells[$cell->getCoordinate()]) || !$cell->hasFlag(static::FLAG_SHIP)) {
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
        if (isset(static::$checkedCells[$cell->getId()])) {
            return true;
        }

        if (!$cell->hasFlag(static::FLAG_DEAD_SHIP)) {
            return false;
        }

        try {
            $processor = new PathProcessor('');

            foreach (static::getShipCells($cell, static::FLAG_DEAD) as $shipCell) {
                static::$checkedCells[$cell->getId()] = $shipCell;

                $processor->setOriginCoordinateFromCell($shipCell);

                foreach ($processor->getAdjacentCells($cell->getBattlefield(), CellModel::FLAG_SHIP) as $waterCell) {
                    static::switchPhase($waterCell, static::FLAG_SKIP);
                }
            }

            return true;
        } catch (CellException $e) {
            return false;
        }
    }
}
