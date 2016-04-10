<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Repository\CellStateRepository;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;

/**
 * @since 2.0
 */
class CellModel
{
    const STATE_WATER_LIVE  = 1;
    const STATE_WATER_DIED  = 2;
    const STATE_SHIP_LIVE   = 3;
    const STATE_SHIP_DIED   = 4;
    const STATE_WATER_SKIP  = 5;
    /** instead of functions, as const array is faster */
    const STATES_WATER  = [self::STATE_WATER_LIVE, self::STATE_WATER_DIED];
    const STATES_SHIP   = [self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED];
    const STATES_LIVE   = [self::STATE_WATER_LIVE, self::STATE_SHIP_LIVE];
    const STATES_DIED   = [self::STATE_WATER_DIED, self::STATE_SHIP_DIED];
    const STATES_ALL    = [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED, self::STATE_WATER_SKIP];
    const STATES_SKIP_STRATEGY_PROCESSING = [self::STATE_WATER_SKIP, self::STATE_WATER_DIED];

    /**
     * @var CellState[]
     */
    private static $cachedStates;
    /**
     * @var Cell[]
     */
    private static $changedCells = [];
    /**
     * @var Cell[]
     */
    private static $checkedCells = [];

    public function __construct(CellStateRepository $repository)
    {
        if (null === self::$cachedStates) {
            self::$cachedStates = $repository->getAllIndexed();
        }
    }

    /**
     * @return CellState[]
     */
    public function getAllStates() : array
    {
        return self::$cachedStates;
    }

    /**
     * @return Cell[]
     */
    public static function getChangedCells() : array
    {
        return self::$changedCells;
    }

    public function switchState(Cell $cell, int $customState = null) : Cell
    {
        switch ($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                self::$changedCells[$cell->getId()] = $cell->setState(self::$cachedStates[$customState ?? self::STATE_WATER_DIED]);
                break;
            case self::STATE_SHIP_LIVE:
                self::$changedCells[$cell->getId()] = $cell->setState(self::$cachedStates[self::STATE_SHIP_DIED]);
                break;
        }

        return $cell;
    }

    public function switchStateToSkipped(Cell $cell) : Cell
    {
        return $this->switchState($cell, self::STATE_WATER_SKIP);
    }

    public function isShipDead(Cell $cell) : bool
    {
        if ($cell->getState()->getId() !== self::STATE_SHIP_DIED) {
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
                if (!in_array($_cell->getState()->getId(), self::STATES_SHIP)) {
                    break;
                }
                if ($_cell->getState()->getId() !== self::STATE_SHIP_DIED) {
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
