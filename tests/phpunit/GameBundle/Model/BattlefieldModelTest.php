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
}
