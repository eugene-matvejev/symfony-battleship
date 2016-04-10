<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\BattlefieldModel;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;

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
        $this->assertCount(100, BattlefieldModel::getLiveCells($battlefield));

        $battlefield->getCellByCoordinate('A1')->setState($this->getDeadShipCellStateMock());
        $this->assertCount(99, BattlefieldModel::getLiveCells($battlefield));
    }

    /**
     * @see BattlefieldModel::hasUnfinishedShips
     * @test
     */
    public function hasUnfinishedShips()
    {
        $battlefield = $this->getBattlefieldMock();
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setState($this->getLiveShipCellStateMock());
        $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setState($this->getDeadShipCellStateMock());
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));
    }
}
