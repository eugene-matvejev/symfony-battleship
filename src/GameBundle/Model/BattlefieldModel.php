<?php

namespace GameBundle\Model;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;

/**
 * @since 2.0
 */
class BattlefieldModel
{
    /**
     * @var Cell[][][]
     */
    private static $cells;

    /**
     * @param Battlefield $battlefield
     * @param int         $x
     * @param int         $y
     *
     * @return Cell|null
     */
    public static function getCellByCoordinates(Battlefield $battlefield, int $x, int $y)
    {
        if(!isset(self::$cells[$battlefield->getId()])) {
            self::$cells[$battlefield->getId()] = [];
            foreach($battlefield->getCells() as $cell) {
                if(!isset(self::$cells[$battlefield->getId()][$cell->getX()])) {
                    self::$cells[$battlefield->getId()][$cell->getX()] = [];
                }
                self::$cells[$battlefield->getId()][$cell->getX()][$cell->getY()] = $cell;
            }
        }

        return isset(self::$cells[$battlefield->getId()][$x][$y]) ? self::$cells[$battlefield->getId()][$x][$y] : null;
    }

    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public static function getLiveCells(Battlefield $battlefield) : array
    {
        $cells = [];
        foreach($battlefield->getCells() as $cell) {
            if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }

    public static function isUnfinished(Battlefield $battlefield) : bool
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getState()->getId() === CellModel::STATE_SHIP_LIVE) {
                return false;
            }
        }

        return true;
    }
}
