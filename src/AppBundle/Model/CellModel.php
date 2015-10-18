<?php

namespace AppBundle\Model;

use AppBundle\Entity\Cell;
use AppBundle\Entity\CellState;

class CellModel
{
    const STATE_WATER_LIVE = 1;
    const STATE_WATER_DIED = 2;
    const STATE_SHIP_LIVE  = 3;
    const STATE_SHIP_DIED  = 4;

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
     * @param CellState[] $states
     *
     * @return $this
     */
    public function setCellStates(array $states)
    {
        $this->cellStates = $states;

        return $this;
    }

    /**
     * @param Cell $cell
     */
    public function switchState(Cell $cell)
    {
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState($this->cellStates[self::STATE_WATER_DIED]);
                break;
            case self::STATE_SHIP_LIVE:
                $cell->setState($this->cellStates[self::STATE_SHIP_DIED]);
                break;
            case self::STATE_WATER_DIED:
            case self::STATE_SHIP_DIED:
            default:
                break;
        }
    }

    /**
     * @param Cell $cell
     *
     * @return \stdClass
     */
    public static function getJSON(Cell $cell)
    {
        $std = new \stdClass();
        $std->x = $cell->getX();
        $std->y = $cell->getY();
        $std->s = $cell->getState()->getId();
        $std->pid = $cell->getBattlefield()->getPlayer()->getId();

        return $std;
    }

    /**
     * @return int[]
     */
    public static function getLiveStates()
    {
        return [self::STATE_WATER_LIVE, self::STATE_SHIP_LIVE];
    }

    /**
     * @return int[]
     */
    public static function getDiedStates()
    {
        return [self::STATE_WATER_DIED, self::STATE_SHIP_DIED];
    }

    /**
     * @return int[]
     */
    public static function getStates()
    {
        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED];
    }
}
