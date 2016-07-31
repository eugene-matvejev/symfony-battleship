<?php

namespace EM\Tests\Environment\Cleaner;

use EM\GameBundle\Model\CellModel;

/**
 * @since 22.5
 */
class CellModelCleaner extends CellModel
{
    public static function resetChangedCells()
    {
        static::$changedCells = [];
    }
}
