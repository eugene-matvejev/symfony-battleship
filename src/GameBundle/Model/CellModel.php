<?php

namespace GameBundle\Model;

use GameBundle\Entity\Cell;
use GameBundle\Entity\CellState;
use GameBundle\Repository\CellStateRepository;
use Doctrine\Common\Persistence\ObjectManager;

class CellModel
{
    /**
     * @var CellStateRepository
     */
    private $cellStateRepository;
    /**
     * @var CellState[]
     */
    private static $cellStates;

    const STATE_WATER_LIVE = 1;
    const STATE_WATER_DIED = 2;
    const STATE_SHIP_LIVE  = 3;
    const STATE_SHIP_DIED  = 4;
    const STATE_WATER_SKIP = 5;

    /**
     * @param ObjectManager $om
     */
    function __construct(ObjectManager $om)
    {
        $this->cellStateRepository = $om->getRepository('GameBundle:CellState');
    }

    /**
     * @return CellState[]
     */
    public function getCellStates() : array
    {
        if(null === self::$cellStates)
            self::$cellStates = $this->cellStateRepository->getStates();

        return self::$cellStates;
    }

    /**
     * @param Cell $cell
     *
     * @return Cell
     */
    public function switchState(Cell $cell) : Cell
    {
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState($this->getCellStates()[self::STATE_WATER_DIED]);
                break;
            case self::STATE_SHIP_LIVE:
                $cell->setState($this->getCellStates()[self::STATE_SHIP_DIED]);
                break;
        }

        return $cell;
    }
    /**
     * @param Cell $cell
     *
     * @return Cell
     */
    public function markAsSkipped(Cell $cell) : Cell
    {
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState($this->getCellStates()[self::STATE_WATER_SKIP]);
                break;
        }

        return $cell;
    }

    /**
     * @param Cell       $cell
     * @param bool|false $ignorePlayer
     *
     * @return \stdClass
     */
    public static function getJSON(Cell $cell, $ignorePlayer = false) : \stdClass
    {
        $std = new \stdClass();
        $std->x = $cell->getX();
        $std->y = $cell->getY();
        $std->s = $cell->getState()->getId();
        if(true !== $ignorePlayer)
            $std->pid = $cell->getBattlefield()->getPlayer()->getId();

        return $std;
    }

    /**
     * @return int[]
     */
    public static function getLiveStates() : array
    {
        return [self::STATE_WATER_LIVE, self::STATE_SHIP_LIVE];
    }

    /**
     * @return int[]
     */
    public static function getDiedStates() : array
    {
        return [self::STATE_WATER_DIED, self::STATE_SHIP_DIED];
    }

    /**
     * @return int[]
     */
    public static function getAllStates() : array
    {
        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED, self::STATE_WATER_SKIP];
    }
}
