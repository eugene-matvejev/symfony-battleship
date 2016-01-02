<?php

namespace GameBundle\Model;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;

class BattlefieldModel
{
    /**
     * @var Cell[][][]
     */
    static $indexed;

    /**
     * @param Battlefield $battlefield
     * @param int $x
     * @param int $y
     *
     * @return Cell
     */
    public static function getCellByCoordinates(Battlefield $battlefield, int $x, int $y)
    {
//        foreach($battlefield->getCells() as $cell) {
//            if($cell->getX() === $x && $cell->getY())
//                return $cell;
//        }
//
//        return null;

        if(!isset(self::$indexed[$battlefield->getId()])) {
            self::$indexed[$battlefield->getId()] = [];
            foreach($battlefield->getCells() as $cell) {
                if(!isset(self::$indexed[$battlefield->getId()][$cell->getX()])) {
                    self::$indexed[$battlefield->getId()][$cell->getX()] = [];
                }
                self::$indexed[$battlefield->getId()][$cell->getX()][$cell->getY()] = $cell;
            }
        }
        return isset(self::$indexed[$battlefield->getId()][$x][$y]) ? self::$indexed[$battlefield->getId()][$x][$y] : null;
    }


    public static function getSize(Battlefield $battlefield) : int
    {
        $int = 0;
        foreach($battlefield->getCells() as $cell) {
            if($cell->getX() === 0) {
                $int++;
            }
        }

        return $int;
    }

    public static function getLiveCells(Battlefield $battlefield) : array
    {
        $arr = [];
        foreach($battlefield->getCells() as $cell) {
            if(in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
                $arr[] = $cell;
            }
        }
        return $arr;
    }
}
