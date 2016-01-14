<?php

namespace EM\GameBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use Symfony\Bridge\Monolog\Logger;

/**
 * @since 2.0
 */
class CellModel
{
    const STATE_WATER_LIVE = 1;
    const STATE_WATER_DIED = 2;
    const STATE_SHIP_LIVE  = 3;
    const STATE_SHIP_DIED  = 4;
    const STATE_WATER_SKIP = 5;
    /**
     * @var Cell[]
     */
    private static $changedCells = [];
    /**
     * @var CellState[]
     */
    private static $cellStates;
    /**
     * @var Logger
     */
    private $logger;

    function __construct(ObjectManager $om, Logger $logger)
    {
        if(null === self::$cellStates) {
            self::$cellStates = $om->getRepository('GameBundle:CellState')->getStates();
        }
        $this->logger = $logger;
    }

    /**
     * @return CellState[]
     */
    public function getCellStates() : array
    {
        return self::$cellStates;
    }

    public function switchState(Cell $cell) : Cell
    {
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState(self::$cellStates[self::STATE_WATER_DIED]);
                self::$changedCells[] = $cell;
                break;
            case self::STATE_SHIP_LIVE:
                $cell->setState(self::$cellStates[self::STATE_SHIP_DIED]);
                self::$changedCells[] = $cell;
                break;
        }

        return $cell;
    }

    public function markSkipped(Cell $cell) : Cell
    {
        $this->logger->addEmergency(__FUNCTION__ .' cell:'. $cell->getId() .' state:'. $cell->getState()->getName());
        switch($cell->getState()->getId()) {
            case self::STATE_WATER_LIVE:
                $cell->setState(self::$cellStates[self::STATE_WATER_SKIP]);
                self::$changedCells[] = $cell;
                break;
        }

        return $cell;
    }

    public static function getJSON(Cell $cell) : \stdClass
    {
        $std = new \stdClass();
        $std->x = $cell->getX();
        $std->y = $cell->getY();
        $std->s = $cell->getState()->getId();
        $std->player = PlayerModel::getJSON($cell->getBattlefield()->getPlayer());

        return $std;
    }

    /**
     * @return Cell[]
     */
    public static function getChangedCells() : array
    {
        return self::$changedCells;
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
