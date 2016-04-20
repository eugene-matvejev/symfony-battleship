<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
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
     * @see AIService::attackCell
     * @test
     */
    public function attackCell()
    {
        $masks = [
            CellModel::MASK_NONE,
            CellModel::MASK_DEAD,
            CellModel::MASK_SHIP,
            CellModel::MASK_DEAD_SHIP,
            CellModel::MASK_SKIP
        ];

        foreach ($masks as $mask) {
            $cell = $this->getCellMock('A1', $mask);
            try {
                $this->invokeNonPublicMethod($this->ai, 'attackCell', [$cell]);
                $this->assertTrue($cell->hasMask(CellModel::MASK_DEAD));
            } catch (AIException $e) {
                $this->assertContains($cell->getMask(), [
                    CellModel::MASK_DEAD,
                    CellModel::MASK_DEAD_SHIP,
                    CellModel::MASK_SKIP
                ]);
                $this->assertEquals($mask, $cell->getMask());
            }
        }
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @expectedException
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function pickCellToAttackExpectedException()
    {
        $cells = [];
        $cell = $this->invokeNonPublicMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertNull($cell);
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @depends attackCell
     */
    public function pickCellToAttack()
    {
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
        $this->invokeProcessCPUTurnMethod(
            [
                CellModel::MASK_DEAD_SHIP => ['A1', 'B1'],
                CellModel::MASK_SHIP => ['C1', 'D1']
            ],
            [
                'A1' => CellModel::MASK_DEAD_SHIP,
                'B1' => CellModel::MASK_DEAD_SHIP,
                'C1' => CellModel::MASK_DEAD_SHIP,
                'D1' => CellModel::MASK_SHIP
            ]
        );
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttack
     */
    public function processCPUTurnVerticalStrategy()
    {
        $this->invokeProcessCPUTurnMethod(
            [
                CellModel::MASK_DEAD_SHIP => ['A1', 'A2'],
                CellModel::MASK_SHIP => ['A3', 'A4']
            ],
            [
                'A1' => CellModel::MASK_DEAD_SHIP,
                'A2' => CellModel::MASK_DEAD_SHIP,
                'A3' => CellModel::MASK_DEAD_SHIP,
                'A4' => CellModel::MASK_SHIP
            ]
        );
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttack
     */
    public function processCPUTurnBothStrategy()
    {
        $this->invokeProcessCPUTurnMethod(
            [
                CellModel::MASK_DEAD_SHIP => ['A1'],
                CellModel::MASK_SHIP => ['A2'],
                CellModel::MASK_DEAD => ['B1']
            ],
            [
                'A1' => CellModel::MASK_DEAD_SHIP,
                'A2' => CellModel::MASK_DEAD_SHIP,
                'B1' => CellModel::MASK_DEAD
            ]
        );
    }

    private function invokeProcessCPUTurnMethod(array $cellsToAlter, array $expectedMasksPerCoordinate)
    {
        $battlefield = $this->getBattlefieldMock();
        foreach ($cellsToAlter as $mask => $coordinates) {
            foreach ($coordinates as $coordinate) {
                $battlefield->getCellByCoordinate($coordinate)->setMask($mask);
            }
        }

        $this->ai->processCPUTurn($battlefield);
        foreach ($battlefield->getCells() as $cell) {
            $this->assertEquals(
                ($expectedMasksPerCoordinate[$cell->getCoordinate()] ?? CellModel::MASK_NONE),
                $cell->getMask(),
                "cell {$cell->getCoordinate()} have unexpected state: {$cell->getMask()}"
            );
        }
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackWaterLiveCell()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_NONE, CellModel::MASK_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackWaterDeadCell()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_DEAD, CellModel::MASK_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackShipLiveCell()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_SHIP, CellModel::MASK_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackShipDeadCell()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_DEAD_SHIP, CellModel::MASK_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCellWaterSkip()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_SKIP, CellModel::MASK_SKIP);
    }

    private function invokeAttackCellMethod(int $cellMask, int $expectedMask)
    {
        $cell = $this->getCellMock('A1', $cellMask);
        $returnedCell = $this->invokeNonPublicMethod($this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedMask, $cell->getMask());
    }
}
