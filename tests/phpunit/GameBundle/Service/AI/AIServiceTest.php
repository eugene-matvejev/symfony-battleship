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
    protected static $ai;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$ai = static::$container->get('battleship_game.service.ai_core');
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function pickCellToAttackExpectedException()
    {
        $this->invokeMethod(static::$ai, 'pickCellToAttack', [[]]);
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
        $cell = $this->invokeMethod(static::$ai, 'pickCellToAttack', [$cells]);
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

    private function invokeProcessCPUTurnMethod(array $coordinatesCollection, array $expectedFlags)
    {
        $battlefield = MockFactory::getBattlefieldMock();
        foreach ($coordinatesCollection as $flag => $coordinates) {
            foreach ($coordinates as $coordinate) {
                $battlefield->getCellByCoordinate($coordinate)->setFlags($flag);
            }
        }

        static::$ai->processCPUTurn($battlefield);

        foreach ($battlefield->getCells() as $cell) {
            $this->assertEquals($expectedFlags[$cell->getCoordinate()] ?? CellModel::FLAG_NONE, $cell->getFlags());
        }
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCellOnFlagNone()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_NONE, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagDead()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_DEAD, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCellOnFlagShip()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagDeadShip()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_DEAD_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagSkip()
    {
        $this->invokeAttackCellMethod(CellModel::FLAG_SKIP, CellModel::FLAG_SKIP);
    }

    protected function invokeAttackCellMethod(int $cellFlag, int $expectedFlag)
    {
        $cell = $this->invokeMethod(static::$ai, 'attackCell', [MockFactory::getCellMock('A1', $cellFlag)]);

        $this->assertEquals($expectedFlag, $cell->getFlags());
    }
}
