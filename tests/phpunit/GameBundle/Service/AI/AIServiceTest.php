<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see AIService
 */
class AIServiceTest extends ExtendedTestSuite
{
    use BattlefieldMockTrait;
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
                $this->invokePrivateMethod($this->ai, 'attackCell', [$cell]);
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
        $cell = $this->invokePrivateMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertNull($cell);

        $cells = [
            $this->getCellMock('A1'),
            $this->getCellMock('A2')
        ];
        $cell = $this->invokePrivateMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertInstanceOf(Cell::class, $cell);
        $this->assertContains($cell->getState()->getId(), CellModel::STATES_DIED);
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttack
     */
    public function processCPUTurnHorizontalStrategy()
    {
        $this->invokeProcessCPUTurnMethod(['A1', 'B1'], ['C1', 'D1'], [], [
            'A1' => CellModel::STATE_SHIP_DIED,
            'B1' => CellModel::STATE_SHIP_DIED,
            'C1' => CellModel::STATE_SHIP_DIED,
            'D1' => CellModel::STATE_SHIP_LIVE
        ]);
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttack
     */
    public function processCPUTurnVerticalStrategy()
    {
        $this->invokeProcessCPUTurnMethod(['A1', 'A2'], ['A3', 'A4'], [], [
            'A1' => CellModel::STATE_SHIP_DIED,
            'A2' => CellModel::STATE_SHIP_DIED,
            'A3' => CellModel::STATE_SHIP_DIED,
            'A4' => CellModel::STATE_SHIP_LIVE
        ]);
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttack
     */
    public function processCPUTurnBothStrategy()
    {
        $this->invokeProcessCPUTurnMethod(['A1'], ['A2'], ['B1'], [
            'A1' => CellModel::STATE_SHIP_DIED,
            'A2' => CellModel::STATE_SHIP_DIED,
            'B1' => CellModel::STATE_WATER_DIED
        ]);
    }

    private function invokeProcessCPUTurnMethod(array $deadShipCoordinates, array $liveShipCoordinates, array $deadWaterCoordinates, array $expected)
    {
        $battlefield = $this->getBattlefieldMock();
        $liveShipState = $this->getLiveShipCellStateMock();
        $deadShipState = $this->getDeadShipCellStateMock();
        $deadWaterState = $this->getDeadWaterCellStateMock();

        foreach ($liveShipCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setState($liveShipState);
        }
        foreach ($deadShipCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setState($deadShipState);
        }
        foreach ($deadWaterCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setState($deadWaterState);
        }

        $this->ai->processCPUTurn($battlefield);

        foreach ($battlefield->getCells() as $cell) {
            $coordinate = $cell->getCoordinate();
            $stateId = $cell->getState()->getId();

            isset($expected[$coordinate])
                ? $this->assertEquals($expected[$coordinate], $stateId, "cell {$coordinate} have unexpected state: {$stateId}")
                : $this->assertEquals(CellModel::STATE_WATER_LIVE, $stateId, "cell {$coordinate} have unexpected state: {$stateId}");
        }
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackWaterLiveCell()
    {
        $this->invokeAttackCellMethod(CellModel::STATE_WATER_LIVE, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackWaterDeadCell()
    {
        $this->invokeAttackCellMethod(CellModel::STATE_WATER_DIED, CellModel::STATE_WATER_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackShipLiveCell()
    {
        $this->invokeAttackCellMethod(CellModel::STATE_SHIP_LIVE, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackShipDeadCell()
    {
        $this->invokeAttackCellMethod(CellModel::STATE_SHIP_DIED, CellModel::STATE_SHIP_DIED);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCellWaterSkip()
    {
        $this->invokeAttackCellMethod(CellModel::STATE_WATER_SKIP, CellModel::STATE_WATER_SKIP);
    }

    private function invokeAttackCellMethod(int $origCellStateId, int $expectedCellStateId)
    {
        $cell = $this->getCellMock('A1', $origCellStateId);
        $returnedCell = $this->invokePrivateMethod($this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}
