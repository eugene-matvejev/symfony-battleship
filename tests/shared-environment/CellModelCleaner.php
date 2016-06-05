<?php

namespace EM\Tests\Environment;

use EM\GameBundle\Model\CellModel;

/**
 * @since 18.1.3    
 */
class CellModelCleaner extends CellModel
{
    public static function resetStaticValues()
    {
        static::$changedCells = [];
        static::$checkedCells = [];
    }
}
