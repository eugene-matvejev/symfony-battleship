<?php

namespace EM\GameBundle\Model;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;

/**
 * @since 2.0
 */
class BattlefieldModel
{
    const INDEX_START = 'A';
    /**
     * @param Battlefield $battlefield
     *
     * @return Cell[]
     */
    public static function getLiveCells(Battlefield $battlefield) : array
    {
        $cells = [];
        foreach ($battlefield->getCells() as $cell) {
            if (!$cell->hasFlag(CellModel::FLAG_DEAD)) {
                $cells[] = $cell;
            }
        }

        return $cells;
    }

    public static function hasUnfinishedShips(Battlefield $battlefield) : bool
    {
        foreach ($battlefield->getCells() as $cell) {
            if ($cell->hasFlag(CellModel::FLAG_SHIP) && !$cell->hasFlag(CellModel::FLAG_DEAD)) {
                return true;
            }
        }

        return false;
    }

    public static function generate(int $size, array $coordinates = []) : Battlefield
    {
        $battlefield = new Battlefield();

        for ($x = 0, $letter = static::INDEX_START; $x < $size; $letter++, $x++) {
            for ($digit = 0; $digit < $size;) {
                $coordinate = $letter . (++$digit);
                $cell = (new Cell())
                    ->setCoordinate($coordinate)
                    ->setFlags(in_array($coordinate, $coordinates) ? CellModel::FLAG_SHIP : CellModel::FLAG_NONE);
                $battlefield->addCell($cell);
            }
        }

        return $battlefield;
    }
}
