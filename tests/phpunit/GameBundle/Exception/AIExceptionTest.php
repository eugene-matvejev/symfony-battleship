<?php

namespace EM\Tests\PHPUnit\GameBundle\Exception;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\PHPUnit\GameBundle\Service\AI\AIServiceTest;

/**
 * @see AIService
 */
class AIExceptionTest extends AIServiceTest
{
    /**
     * @see AIService::chooseCellToAttack
     * @test
     */
    public function attackCellWithCellState_1()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_LIVE, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::chooseCellToAttack
     * @test
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackCellWithCellState_2()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_DIED, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::chooseCellToAttack
     * @test
     */
    public function attackCellWithCellState_3()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_LIVE, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::chooseCellToAttack
     * @test
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackCellWithCellState_4()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::chooseCellToAttack
     * @test
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function attackCellWithCellState_5()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_SKIP, CellModel::STATE_WATER_SKIP);
    }

    /**
     * @param int $origCellStateId
     * @param int $expectedCellStateId
     *
     * @throws AIException
     */
    private function simulateAttackCell(int $origCellStateId, int $expectedCellStateId)
    {
        $cell = $this->getMockedCell($origCellStateId);
        $returnedCell = $this->invokePrivateMethod(AIService::class, $this->ai, 'chooseCellToAttack', ['cells' => [$cell]]);

        $this->assertInstanceOf(Cell::class, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}
