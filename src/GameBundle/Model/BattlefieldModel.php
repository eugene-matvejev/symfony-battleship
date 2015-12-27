<?php

namespace GameBundle\Model;

use GameBundle\Entity\Battlefield;
use GameBundle\Entity\Cell;

class BattlefieldModel
{
    /**
     * @param Battlefield $battlefield
     * @param int $x
     * @param int $y
     *
     * @return Cell
     */
    public static function getCellByCoordinates(Battlefield $battlefield, \int $x, \int $y)
    {
        foreach($battlefield->getCells() as $cell) {
            if($cell->getX() === $x && $cell->getY() === $y) {
                return $cell;
            }
        }
    }


    public static function getSize(Battlefield $battlefield) : \int
    {
        $int = 0;
        foreach($battlefield->getCells() as $cell) {
            if($cell->getX() === 0) {
                $int++;
            }
        }

        return $int;
    }
}
