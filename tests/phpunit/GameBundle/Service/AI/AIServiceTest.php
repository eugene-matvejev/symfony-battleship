<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see AIService
 */
class AIServiceTest extends IntegrationTestSuite
{
    /**
     * @var AIService
     */
    protected $ai;

    protected function setUp()
    {
        $this->ai = static::$container->get('battleship_game.service.ai_core');
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function pickCellToAttackExpectedException()
    {
        $cells = [];
        $this->invokeMethod($this->ai, 'pickCellToAttack', [$cells]);
    }

    /**
     * @see AIService::pickCellToAttack
     * @test
     */
    public function pickCellToAttack()
    {
        $cells = [
            MockFactory::getCellMock('A1'),
            MockFactory::getCellMock('A2')
        ];
        /** @var Cell $cell */
        $cell = $this->invokeMethod($this->ai, 'pickCellToAttack', [$cells]);
        $this->assertInstanceOf(Cell::class, $cell);
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD));
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
                CellModel::FLAG_DEAD_SHIP => ['A1', 'B1'],
                CellModel::FLAG_SHIP      => ['C1', 'D1']
            ],
            [
                'A1' => CellModel::FLAG_DEAD_SHIP,
                'B1' => CellModel::FLAG_DEAD_SHIP,
                'C1' => CellModel::FLAG_DEAD_SHIP,
                'D1' => CellModel::FLAG_SHIP
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
                CellModel::FLAG_DEAD_SHIP => ['A1', 'A2'],
                CellModel::FLAG_SHIP      => ['A3', 'A4']
            ],
            [
                'A1' => CellModel::FLAG_DEAD_SHIP,
                'A2' => CellModel::FLAG_DEAD_SHIP,
                'A3' => CellModel::FLAG_DEAD_SHIP,
                'A4' => CellModel::FLAG_SHIP
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
                CellModel::FLAG_DEAD_SHIP => ['A1'],
                CellModel::FLAG_SHIP      => ['A2'],
                CellModel::FLAG_DEAD      => ['B1']
            ],
            [
                'A1' => CellModel::FLAG_DEAD_SHIP,
                'A2' => CellModel::FLAG_DEAD_SHIP,
                'B1' => CellModel::FLAG_DEAD
            ]
        );
    }

    private function invokeProcessCPUTurnMethod(array $cellsToAlter, array $expectedMasks)
    {
        $battlefield = MockFactory::getBattlefieldMock();
        foreach ($cellsToAlter as $mask => $coordinates) {
            foreach ($coordinates as $coordinate) {
                $battlefield->getCellByCoordinate($coordinate)->setFlags($mask);
            }
        }

        $this->ai->processCPUTurn($battlefield);
        foreach ($battlefield->getCells() as $cell) {
            $this->assertEquals(
                $expectedMasks[$cell->getCoordinate()] ?? CellModel::FLAG_NONE,
                $cell->getFlags(),
                "cell {$cell->getCoordinate()} have unexpected state: {$cell->getFlags()}"
            );
        }
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCell_FLAG_NONE()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_NONE, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_FLAG_DEAD()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_DEAD, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCell_FLAG_SHIP()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_FLAG_DEAD_SHIP()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_DEAD_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_FLAG_SKIP()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_SKIP, CellModel::FLAG_SKIP);
    }

    private function invokeAttackCellMethod(int $cellMask, int $expectedMask)
    {
        $cell = MockFactory::getCellMock('A1', $cellMask);
        $returnedCell = $this->invokeMethod($this->ai, 'attackCell', [$cell]);

        $this->assertSame($cell, $returnedCell);
        $this->assertEquals($expectedMask, $cell->getFlags());
    }
}
