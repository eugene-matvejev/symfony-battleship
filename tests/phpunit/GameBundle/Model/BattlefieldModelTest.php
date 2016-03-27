<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\BattlefieldModel;
use EM\GameBundle\Model\CellModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see BattlefieldModel
 */
class BattlefieldModelTest extends ExtendedTestCase
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

        $battlefield->getCellByCoordinate('A1')->setState($this->getCellStateMock(CellModel::STATE_SHIP_DIED));
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

        $battlefield->getCellByCoordinate('A1')->setState($this->getCellStateMock(CellModel::STATE_SHIP_LIVE));
        $this->assertTrue(BattlefieldModel::hasUnfinishedShips($battlefield));

        $battlefield->getCellByCoordinate('A1')->setState($this->getCellStateMock(CellModel::STATE_SHIP_DIED));
        $this->assertFalse(BattlefieldModel::hasUnfinishedShips($battlefield));
    }
}
