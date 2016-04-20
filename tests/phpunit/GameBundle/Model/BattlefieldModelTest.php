<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see BattlefieldModel
 */
class BattlefieldModelTest extends \PHPUnit_Framework_TestCase
{
    use BattlefieldMockTrait;

    /**
     * @see BattlefieldModel::getLiveCells
     * @test
     */
    public function getLiveCells()
    {
        $battlefield = $this->getBattlefieldMock();
        $this->assertCount(49, BattlefieldModel::getLiveCells($battlefield));

        $battlefield->getCellByCoordinate('A1')->addMask(CellModel::MASK_DEAD);
        $this->assertCount(48, BattlefieldModel::getLiveCells($battlefield));
    }

    /**
     * @see BattlefieldModel::hasUnfinishedShips
     * @test
     */
    public function hasUnfinishedShips()
    {
        $battlefield = $this->getBattlefieldMock();
        /** by default all cells are mocked as 'live water' */
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setMask(CellModel::MASK_SHIP);
        $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setMask(CellModel::MASK_DEAD_SHIP);
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));
    }
}
