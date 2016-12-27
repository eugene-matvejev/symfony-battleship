<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Exception\AIException;
use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIService;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see AIService
 */
class AIServiceTest extends AbstractKernelTestSuite
{
    /**
     * @var AIService
     */
    private static $ai;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$ai = static::$container->get('em.game_bundle.service.ai_core');
    }

    /**
     * @see     AIService::pickCellToAttack
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function pickCellToAttackExpectedException()
    {
        $this->pickCellToAttack([]);
    }

    /**
     * @see AIService::pickCellToAttack
     * @test
     */
    public function pickCellToAttackExpectedCell()
    {
        $cell = $this->pickCellToAttack([MockFactory::getCellMock('A1'), MockFactory::getCellMock('A2')]);
        $this->assertTrue($cell->hasFlag(CellModel::FLAG_DEAD));
    }

    /**
     * @see AIService::pickCellToAttack
     *
     * @param Cell[] $cells
     *
     * @return Cell
     * @throws CellException
     */
    private function pickCellToAttack(array $cells) : Cell
    {
        return $this->invokeMethod(static::$ai, 'pickCellToAttack', [$cells]);
    }

    /**
     * @see     AIService::processCPUTurn
     * @test
     *
     * @depends pickCellToAttackExpectedException
     * @depends pickCellToAttackExpectedCell
     */
    public function processCPUTurnHorizontalStrategy()
    {
        $this->assertProcessCPUTurnResult(
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
     * @depends pickCellToAttackExpectedException
     * @depends pickCellToAttackExpectedCell
     */
    public function processCPUTurnVerticalStrategy()
    {
        $this->assertProcessCPUTurnResult(
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
     * @depends pickCellToAttackExpectedException
     * @depends pickCellToAttackExpectedCell
     */
    public function processCPUTurnBothStrategy()
    {
        $this->assertProcessCPUTurnResult(
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

    /**
     * @see     AIService::processCPUTurn
     *
     * @param array $coordinatesCollection
     * @param array $expectedFlags
     */
    private function assertProcessCPUTurnResult(array $coordinatesCollection, array $expectedFlags)
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
        $this->attackCell(CellModel::FLAG_NONE, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagDead()
    {
        $this->attackCell(CellModel::FLAG_DEAD, CellModel::FLAG_DEAD);
    }

    /**
     * @see AIService::attackCell
     * @test
     */
    public function attackCellOnFlagShip()
    {
        $this->attackCell(CellModel::FLAG_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagDeadShip()
    {
        $this->attackCell(CellModel::FLAG_DEAD_SHIP, CellModel::FLAG_DEAD_SHIP);
    }

    /**
     * @see AIService::attackCell
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\AIException
     */
    public function expectedExceptionOnAttackCellOnFlagSkip()
    {
        $this->attackCell(CellModel::FLAG_SKIP, CellModel::FLAG_SKIP);
    }

    /**
     * @see AIService::attackCell
     *
     * @param int $cellFlag
     * @param int $expectedFlag
     *
     * @throws AIException
     */
    private function attackCell(int $cellFlag, int $expectedFlag)
    {
        $cell = $this->invokeMethod(static::$ai, 'attackCell', [MockFactory::getCellMock('A1', $cellFlag)]);

        $this->assertEquals($expectedFlag, $cell->getFlags());
    }
}
