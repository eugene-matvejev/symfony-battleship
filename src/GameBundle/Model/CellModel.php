<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;

/**
 * @since 2.0
 */
class CellModel
{
    const STATE_WATER_LIVE = 1;
    const STATE_WATER_DIED = 2;
    const STATE_SHIP_LIVE = 3;
    const STATE_SHIP_DIED = 4;
    const STATE_WATER_SKIP = 5;
    /**
     * @var CellState[]
     */
    private static $cellStates;
    /**
     * @var Cell[]
     */
    private static $changedCells = [];
    /**
     * @var Cell[][][]
     */
    private static $cachedCells;

    function __construct(ObjectManager $om)
    {
        if (null === self::$cellStates) {
            self::$cellStates = $om->getRepository('GameBundle:CellState')->getStates();
        }
    }

    /**
     * @return CellState[]
     */
    public function getCellStates() : array
    {
        return self::$cellStates;
    }

    /**
     * @return Cell[]
     */
    public static function getChangedCells() : array
    {
        return self::$changedCells;
    }

    public function switchState(Cell $cell) : Cell
    {
        switch ($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState(self::$cellStates[self::STATE_WATER_DIED]);
                self::$changedCells[] = $cell;
                break;
            case self::STATE_SHIP_LIVE:
                $cell->setState(self::$cellStates[self::STATE_SHIP_DIED]);
                self::$changedCells[] = $cell;
                break;
        }

        return $cell;
    }

    public function switchStateToSkipped(Cell $cell) : Cell
    {
        switch ($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState(self::$cellStates[self::STATE_WATER_SKIP]);
                self::$changedCells[] = $cell;
                break;
        }

        return $cell;
    }

    /**
     * @param Battlefield $battlefield
     * @param int         $x
     * @param int         $y
     *
     * @return Cell|null
     */
    public static function getByCoordinates(Battlefield $battlefield, int $x, int $y)
    {
        if (!isset(self::$cachedCells[$battlefield->getId()])) {
            self::$cachedCells[$battlefield->getId()] = [];

            foreach ($battlefield->getCells() as $cell) {
                if (!isset(self::$cachedCells[$battlefield->getId()][$cell->getX()])) {
                    self::$cachedCells[$battlefield->getId()][$cell->getX()] = [];
                }

                self::$cachedCells[$battlefield->getId()][$cell->getX()][$cell->getY()] = $cell;
            }
        }

        return self::$cachedCells[$battlefield->getId()][$x][$y] ?? null;
    }

    public static function getJSON(Cell $cell) : \stdClass
    {
        return (object)[
            'x' => $cell->getX(),
            'y' => $cell->getY(),
            's' => $cell->getState()->getId(),
            'player' => PlayerModel::getJSON($cell->getBattlefield()->getPlayer())
        ];
    }

    /**
     * @return int[]
     */
    public static function getShipStates() : array
    {
        return [self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED];
    }

    /**
     * @return int[]
     */
    public static function getWaterStates() : array
    {
        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED];
    }

    /**
     * @return int[]
     */
    public static function getLiveStates() : array
    {
        return [self::STATE_WATER_LIVE, self::STATE_SHIP_LIVE];
    }

    /**
     * @return int[]
     */
    public static function getDiedStates() : array
    {
        return [self::STATE_WATER_DIED, self::STATE_SHIP_DIED];
    }

    /**
     * @return int[]
     */
    public static function getAllStates() : array
    {
        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED, self::STATE_WATER_SKIP];
    }
}
