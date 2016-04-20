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
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function pickCellToAttackExpectedException()
    {
        $cells = [];
        $this->invokeNonPublicMethod($this->ai, 'pickCellToAttack', [$cells]);
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
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

    private function invokeProcessCPUTurnMethod(array $cellsToAlter, array $expectedMasks)
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
                ($expectedMasks[$cell->getCoordinate()] ?? CellModel::MASK_NONE),
                $cell->getMask(),
                "cell {$cell->getCoordinate()} have unexpected state: {$cell->getMask()}"
            );
        }
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCell_MASK_NONE()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_NONE, CellModel::MASK_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_MASK_DEAD()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_DEAD, CellModel::MASK_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCell_MASK_SHIP()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_SHIP, CellModel::MASK_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_MASK_DEAD_SHIP()
    {
        $this->invokeAttackCellMethod(CellModel::MASK_DEAD_SHIP, CellModel::MASK_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function exceptionOnAttackCell_MASK_SKIP()
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
