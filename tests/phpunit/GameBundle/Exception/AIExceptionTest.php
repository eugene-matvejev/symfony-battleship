<?php

namespace EM\Tests\PHPUnit\GameBundle\Exception;

use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\CellMockTrait;
use EM\Tests\PHPUnit\GameBundle\Service\AI\AIServiceTest;

/**
 * @see AIService
 */
class AIExceptionTest extends AIServiceTest
{
    use CellMockTrait;

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackWaterLiveCell()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_LIVE, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackWaterDeadCell()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_DIED, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackShipLiveCell()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_LIVE, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackShipDeadCell()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackCellWaterSkip()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_SKIP, CellModel::STATE_WATER_SKIP);
    }

    /**
     * @see AIService::attackCell
     *
     * @param int $origCellStateId
     * @param int $expectedCellStateId
     *
     * @throws AIException
     */
    private function simulateAttackCell(int $origCellStateId, int $expectedCellStateId)
    {
        $cell = $this->getCellMock('A1', $origCellStateId);
        $returnedCell = $this->invokePrivateMethod(AIService::class, $this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}
