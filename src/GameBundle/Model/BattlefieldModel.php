<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;

/**
 * @since 2.0
 */
class BattlefieldModel
{
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
