<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory;

/**
 * @see BattlefieldModel
 */
class BattlefieldModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @see BattlefieldModel::getLiveCells
     * @test
     */
    public function getLiveCells()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $this->assertCount(49, BattlefieldModel::getLiveCells($battlefield));

        $battlefield->getCellByCoordinate('A1')->addFlag(CellModel::FLAG_DEAD);
        $this->assertCount(48, BattlefieldModel::getLiveCells($battlefield));
    }

    /**
     * @see BattlefieldModel::hasUnfinishedShips
     * @test
     */
    public function hasUnfinishedShips()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        /** by default all cells are mocked as 'live water' */
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_SHIP);
        $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));
    }

    /**
     * @see BattlefieldModel::generate
     * @test
     */
    public function generate()
    {
        $shipCells   = ['A1', 'A3', 'A5'];
        $battlefield = BattlefieldModel::generate(7, $shipCells);

        $this->assertCount(49, $battlefield->getCells());
        foreach ($battlefield->getCells() as $cell) {
            $expectedFlag = in_array($cell->getCoordinate(), $shipCells)
                ? CellModel::FLAG_SHIP
                : CellModel::FLAG_NONE;

            $this->assertEquals($expectedFlag, $cell->getFlags());
        }
    }

    /**
     * @see BattlefieldModel::flagWaterAroundShip
     * @test
     */
    public function flagWaterAroundShipOnNonDeadAround()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $cell        = $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_SHIP);

        BattlefieldModel::flagWaterAroundShip($cell);

        foreach (['A1', 'A2', 'A3', 'B1', 'B3', 'C1', 'C2', 'C3'] as $coordinate) {
            $this->assertTrue($battlefield->getCellByCoordinate($coordinate)->hasFlag(CellModel::FLAG_SKIP));
        }
    }

    /**
     * @see BattlefieldModel::flagWaterAroundShip
     * @test
     */
    public function flagWaterAroundShipOnA1AlreadyDead()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A1')->setFlags(CellModel::FLAG_DEAD);
        $cell = $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_SHIP);

        BattlefieldModel::flagWaterAroundShip($cell);

        foreach (['A2', 'A3', 'B1', 'B3', 'C1', 'C2', 'C3'] as $coordinate) {
            $this->assertTrue($battlefield->getCellByCoordinate($coordinate)->hasFlag(CellModel::FLAG_SKIP));
        }
    }
}
