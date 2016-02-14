<?php

namespace EM\Tests\PHPUnit\GameBundle\AI;

use EM\GameBundle\AI\AI;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;


/**
 * @see EM\GameBundle\AI\AI
 */
class AIExceptionTest extends AITest
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @test
     */
    public function attackCellWithCellState_1()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_LIVE, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @expectedException \EM\GameBundle\Exception\AIException
     * @test
     */
    public function attackCellwithCellState_2()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_DIED, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @test
     */
    public function attackCellWithCellState_3()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_LIVE, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @expectedException \EM\GameBundle\Exception\AIException
     * @test
     */
    public function attackCellwithCellState_4()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see EM\GameBundle\AI\AI::chooseCellToAttack
     * @expectedException \EM\GameBundle\Exception\AIException
     * @test
     */
    public function attackCellwithCellState_5()
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
        $returnedCell = $this->invokePrivateMethod(AI::class, $this->ai, 'chooseCellToAttack', ['cells' => [$cell]]);

        $this->assertInstanceOf(Cell::class, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}