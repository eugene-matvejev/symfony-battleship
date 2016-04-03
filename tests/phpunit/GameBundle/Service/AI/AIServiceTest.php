<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\CellMockTrait;

/**
 * @see AIService
 */
class AIServiceTest extends ExtendedTestSuite
{
    use CellMockTrait;
    /**
     * @var AIService
     */
    protected $ai;

    protected function setUp()
    {
        parent::setUp();
        $this->ai = $this->getContainer()->get('battleship.game.services.ai.core.service');
    }

    /**
     * @see     AIService::attackCell
     * @test
     */
    public function attackCell()
    {
        $statesWithExpectedException = array_merge(CellModel::STATES_DIED, [CellModel::STATE_WATER_SKIP]);

        foreach (CellModel::STATES_ALL as $cellStateId) {
            $cell = $this->getCellMock($cellStateId);
            try {
                $previousCellStateId = $cell->getState()->getId();
                $this->invokePrivateMethod(AIService::class, $this->ai, 'attackCell', [$cell]);
                $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED);
                $this->assertNotContains($previousCellStateId, $statesWithExpectedException);
            } catch (AIException $e) {
                $this->assertContains($cell->getState()->getId(), $statesWithExpectedException);
            }
        }
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @depends attackCell
     */
    public function pickCellToAttack()
    {
        $cells = [];
        $cell = $this->invokePrivateMethod(AIService::class, $this->ai, 'pickCellToAttack', [$cells]);
        $this->assertNull($cell);

        $cells = [
            $this->getCellMock('A1'),
            $this->getCellMock('A2')
        ];
        $cell = $this->invokePrivateMethod(AIService::class, $this->ai, 'pickCellToAttack', [$cells]);
        $this->assertInstanceOf(Cell::class, $cell);
        $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED);
    }

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
    public function exceptionOnAttackWaterDeadCell()
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
    public function exceptionOnAttackShipDeadCell()
    {
        $this->simulateAttackCell(CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCellWaterSkip()
    {
        $this->simulateAttackCell(CellModel::STATE_WATER_SKIP, CellModel::STATE_WATER_SKIP);
    }

    private function simulateAttackCell(int $origCellStateId, int $expectedCellStateId)
    {
        $cell = $this->getCellMock('A1', $origCellStateId);
        $returnedCell = $this->invokePrivateMethod(AIService::class, $this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}
