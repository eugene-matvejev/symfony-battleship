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
        foreach ($battlefield->getCells() as $cell) {
            if (!$cell->hasMask(CellModel::MASK_DEAD)) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }

    public static function hasUnfinishedShips(Battlefield $battlefield) : bool
    {
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->hasMask(CellModel::MASK_SHIP)) {
                if (!$cell->hasMask(CellModel::MASK_DEAD)) {
                    return true;
                }
            }
        }

        return false;
    }
}
