<?php

namespace AppBundle\Model;

use AppBundle\Entity\Cell;
use AppBundle\Entity\CellState;

class CellStateModel {
    const WATER_LIVE = 1;
    const WATER_DIED = 2;
    const SHIP_LIVE  = 3;
    const SHIP_DIED  = 4;

    /**
     * @var CellState[]
     */
    private $cellStates;

    /**
     * @return CellState[]
     */
    public function getCellStates()
    {
        return $this->cellStates;
    }

    /**
     * @param CellState[] $cellStates
     *
     * @return $this
     */
    public function setCellStates(array $cellStates)
    {
        $this->cellStates = $cellStates;

        return $this;
    }

    /**
     * @param Cell $cell
     */
    public function swapStatus(Cell $cell)
    {
        switch($cell->getState()->getId()) {
            case self::WATER_LIVE:
                $cell->setState($this->cellStates[self::WATER_DIED]);
                break;
            case self::SHIP_LIVE:
                $cell->setState($this->cellStates[self::SHIP_DIED]);
                break;
            case self::WATER_DIED:
            case self::SHIP_DIED:
            default:
                break;
        }
    }
}
