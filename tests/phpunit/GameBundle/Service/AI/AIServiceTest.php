<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\Environment\ContainerAwareTestSuite;
use EM\Tests\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see AIService
 */
class AIServiceTest extends ContainerAwareTestSuite
{
    use BattlefieldMockTrait;
    /**
     * @var AIService
     */
    protected $ai;

    protected function setUp()
    {
        parent::setUp();
        $this->ai = static::$container->get('battleship.game.services.ai.core.service');
    }

    /**
     * @see     AIService::attackCell
     * @test
     */
    public function attackCell()
    {
        $statesWithExpectedException = array_merge(CellModel::STATES_DIED, [CellModel::STATE_WATER_SKIP]);
        $masks = [
            CellModel::MASK_NONE,
            CellModel::MASK_DEAD,
            CellModel::MASK_SHIP,
            CellModel::MASK_DEAD_SHIP,
            CellModel::MASK_SKIP
        ]
        foreach ($masks as $mask) {
            $cell = $this->getCellMock('A1', $mask);
            try {
                $previousCellStateId = $cell->getState()->getId();
                $this->invokeNonPublicMethod($this->ai, 'attackCell', [$cell]);
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
        $cell = $this->invokeNonPublicMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertNull($cell);

        $cells = [
            $this->getCellMock('A1'),
            $this->getCellMock('A2')
        ];
        /** @var Cell $cell */
        $cell = $this->invokeNonPublicMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertInstanceOf(Cell::class, $cell);
        $this->assertTrue($cell->hasMask(CellModel::MASK_DEAD));
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

        foreach ($liveShipCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setMask(CellModel::MASK_SHIP);
        }
        foreach ($deadShipCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setMask(CellModel::MASK_DEAD_SHIP);
        }
        foreach ($deadWaterCoordinates as $coordinate) {
            $battlefield->getCellByCoordinate($coordinate)->setMask(CellModel::MASK_DEAD);
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
        $returnedCell = $this->invokeNonPublicMethod($this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedCellStateId, $cell->getState()->getId());
    }
}
