<?php

namespace AppBundle\Model;

use AppBundle\Entity\Cell;
use Doctrine\Bundle\DoctrineBundle\Registry;

class CellModel
{
    /**
     * @var \AppBundle\Repository\CellStateRepository
     */
    private $cellStateRepository;

    /**
     * @var \AppBundle\Entity\CellState[]
     */
    private static $cellStates;

    const STATE_WATER_LIVE = 1;
    const STATE_WATER_DIED = 2;
    const STATE_SHIP_LIVE  = 3;
    const STATE_SHIP_DIED  = 4;

    /**
     * @param Registry $doctrine
     */
    function __construct(Registry $doctrine)
    {
        $this->cellStateRepository = $doctrine->getRepository('AppBundle:CellState');
    }

    /**
     * @return \AppBundle\Entity\CellState[]
     */
    public function getCellStates() : array
    {
        if(empty(self::$cellStates))
            self::$cellStates = $this->cellStateRepository->getStates();

        return self::$cellStates;
    }

    /**
     * @param Cell $cell
     */
    public function switchState(Cell $cell)
    {
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState($this->getCellStates()[self::STATE_WATER_DIED]);
                break;
            case self::STATE_SHIP_LIVE:
                $cell->setState($this->getCellStates()[self::STATE_SHIP_DIED]);
                break;
            case self::STATE_WATER_DIED:
            case self::STATE_SHIP_DIED:
            default:
                break;
        }
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
        if(!$ignorePlayer)
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
        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED];
    }
}
