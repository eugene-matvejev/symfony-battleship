<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Entity\CellState;
use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;

class BattlefieldModelTest extends ExtendedTestCase
{
//    /**
//     * @var BattlefieldModel
//     */
//    protected $battlefieldModel;
//
//    protected function setUp()
//    {
//        parent::setUp();
//        $this->battlefieldModel = $this->getContainer()->get('battleship.mo');
//    }
//    public static function getJSON(Battlefield $battlefield) : \stdClass
//    {
//        $std = new \stdClass();
//        $std->id = $battlefield->getId();
//        $std->player = PlayerModel::getJSON($battlefield->getPlayer());
//        $std->cells = [];
//
//        foreach ($battlefield->getCells() as $cell) {
//            $std->cells[] = CellModel::getJSON($cell);
//        }
//
//        return $std;
//    }
//
//    /**
//     * @param Battlefield $battlefield
//     *
//     * @return Cell[]
//     */
//    public static function getLiveCells(Battlefield $battlefield) : array
//    {
//        $cells = [];
//        foreach ($battlefield->getCells() as $cell) {
//            if (in_array($cell->getState()->getId(), CellModel::getLiveStates())) {
//                $cells[] = $cell;
//            }
//        }
//
//        return $cells;
//    }
//

    /**
     * @see
     * @test
     */
    public function getLiveCells()
    {

    }

    public function isUnfinished()
    {
        $battlefield = $this->getMockedBattlefield();
        $this->assertFalse(BattlefieldModel::isUnfinished($battlefield));

        $cellState = (new CellState())
            ->setName('test cell state')
            ->setId(CellModel::STATE_SHIP_DIED);
        foreach ($battlefield->getCells() as $cell) {
            $cell->setState($cellState);
        }
        $this->assertTrue(BattlefieldModel::isUnfinished($battlefield));
    }

    private function getMockedBattlefield()
    {
        $battlefield = new Battlefield();
        for ($x = 0; $x < 9; $x++) {
            for ($y = 0; $y < 9; $y++) {
                $cell = (new Cell())
                    ->setX($x)
                    ->setY($y);
                $cell->setBattlefield($battlefield);
            }
        }

        return $battlefield;
    }
}