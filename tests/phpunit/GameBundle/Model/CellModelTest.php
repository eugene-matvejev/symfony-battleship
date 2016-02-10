<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

/**
 * @see EM\GameBundle\Model\CellModel
 */
class CellModelTest extends ExtendedTestCase
{
    /**
     * @see EM\GameBundle\Model\CellModel::getCellStates()
     * @test
     */
    public function getCellStates()
    {

        $statesFromDatabase = (new CellModel($this->getObjectManager()))->getCellStates();
        foreach ($statesFromDatabase as $state) {
            $this->assertContains($state->getId(), CellModel::getAllStates());
        }

        $this->assertEquals(count($statesFromDatabase), count(CellModel::getAllStates()));
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getShipStates()
     * @test
     */
    public function getShipStates()
    {
        foreach (CellModel::getShipStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getLiveStates()
     * @test
     */
    public function getLiveStates()
    {
        foreach (CellModel::getLiveStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getDiedStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getDiedStates()
     * @test
     */
    public function getDiedStates()
    {
        foreach (CellModel::getDiedStates() as $state) {
            $this->assertContains($state, CellModel::getAllStates());
            $this->assertNotContains($state, CellModel::getLiveStates());
        }
    }

    /**
     * @see EM\GameBundle\Model\CellModel::getAllStates()
     * @test
     */
    public function getAllStates()
    {
        $diedStates = count(CellModel::getDiedStates());
        $liveStates = count(CellModel::getLiveStates());
        $totalStates = count(CellModel::getAllStates());

        $this->assertGreaterThanOrEqual($diedStates + $liveStates, $totalStates);
    }
}

//class CellModel
//{
//    const STATE_WATER_LIVE = 1;
//    const STATE_WATER_DIED = 2;
//    const STATE_SHIP_LIVE  = 3;
//    const STATE_SHIP_DIED  = 4;
//    const STATE_WATER_SKIP = 5;
//    /**
//     * @var CellState[]
//     */
//    private static $cellStates;
//    /**
//     * @var Cell[]
//     */
//    private static $changedCells = [];
//    /**
//     * @var Cell[][][]
//     */
//    private static $cachedCells;
//
//    function __construct(ObjectManager $om)
//    {
//        if(null === self::$cellStates) {
//            self::$cellStates = $om->getRepository('GameBundle:CellState')->getStates();
//        }
//    }
//
//    /**
//     * @return CellState[]
//     */
//    public function getCellStates() : array
//    {
//        return self::$cellStates;
//    }
//
//    /**
//     * @return Cell[]
//     */
//    public static function getChangedCells() : array
//    {
//        return self::$changedCells;
//    }
//
//    public function switchState(Cell $cell) : Cell
//    {
//        switch($cell->getState()->getId()) {
//            case self::STATE_WATER_LIVE:
//                $cell->setState(self::$cellStates[self::STATE_WATER_DIED]);
//                self::$changedCells[] = $cell;
//                break;
//            case self::STATE_SHIP_LIVE:
//                $cell->setState(self::$cellStates[self::STATE_SHIP_DIED]);
//                self::$changedCells[] = $cell;
//                break;
//        }
//
//        return $cell;
//    }
//
//    public function switchStateToSkipped(Cell $cell) : Cell
//    {
//        switch($cell->getState()->getId()) {
//            case self::STATE_WATER_LIVE:
//                $cell->setState(self::$cellStates[self::STATE_WATER_SKIP]);
//                self::$changedCells[] = $cell;
//                break;
//        }
//
//        return $cell;
//    }
//
//    /**
//     * @param Battlefield $battlefield
//     * @param int         $x
//     * @param int         $y
//     *
//     * @return Cell|null
//     */
//    public static function getByCoordinates(Battlefield $battlefield, int $x, int $y)
//    {
//        if (!isset(self::$cachedCells[$battlefield->getId()])) {
//            self::$cachedCells[$battlefield->getId()] = [];
//
//            foreach ($battlefield->getCells() as $cell) {
//                if (!isset(self::$cachedCells[$battlefield->getId()][$cell->getX()])) {
//                    self::$cachedCells[$battlefield->getId()][$cell->getX()] = [];
//                }
//
//                self::$cachedCells[$battlefield->getId()][$cell->getX()][$cell->getY()] = $cell;
//            }
//        }
//
//        return self::$cachedCells[$battlefield->getId()][$x][$y] ?? null;
//    }
//
//    public static function getJSON(Cell $cell) : \stdClass
//    {
//        return (object)[
//            'x' => $cell->getX(),
//            'y' => $cell->getY(),
//            's' => $cell->getState()->getId(),
//            'player' => PlayerModel::getJSON($cell->getBattlefield()->getPlayer())
//        ];
//    }
//
//    /**
//     * @return int[]
//     */
//    public static function getShipStates() : array
//    {
//        return [self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED];
//    }
//
//    /**
//     * @return int[]
//     */
//    public static function getLiveStates() : array
//    {
//        return [self::STATE_WATER_LIVE, self::STATE_SHIP_LIVE];
//    }
//
//    /**
//     * @return int[]
//     */
//    public static function getDiedStates() : array
//    {
//        return [self::STATE_WATER_DIED, self::STATE_SHIP_DIED];
//    }
//
//    /**
//     * @return int[]
//     */
//    public static function getAllStates() : array
//    {
//        return [self::STATE_WATER_LIVE, self::STATE_WATER_DIED, self::STATE_SHIP_LIVE, self::STATE_SHIP_DIED, self::STATE_WATER_SKIP];
//    }
//}
