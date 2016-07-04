<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Battlefield;
use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyProcessor;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see AIStrategyProcessor
 */
class AIStrategyProcessorTest extends IntegrationTestSuite
{
    /**
     * @var AIStrategyProcessor
     */
    private static $strategyProcessor;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$strategyProcessor = static::$container->get('battleship_game.service.ai_strategy_processor');
    }

    /**
     * @see AIStrategyProcessor::processPath
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function processPathExpectedExceptionOnNotExistingCell()
    {
        $battlefield = MockFactory::getBattlefieldMock();

        $this->invokeProcessPathMethod($battlefield, PathProcessor::PATH_UP, 'A1');
    }

    /**
     * @see AIStrategyProcessor::processPath
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function processPathExpectedExceptionOnDeadNonShipCell()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_SKIP);

        $this->invokeProcessPathMethod($battlefield, PathProcessor::PATH_DOWN, 'A1');
    }

    /**
     * @see AIStrategyProcessor::processPath
     * @test
     */
    public function processPathOnValidCell()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_SHIP);

        /** @var Cell $cell */
        $cell = $this->invokeProcessPathMethod($battlefield, PathProcessor::PATH_DOWN, 'A1');
        $this->assertEquals($cell->getCoordinate(), 'A2');
    }

    /**
     * @see AIStrategyProcessor::processPath
     * @test
     */
    public function processPathOnValidDeadShipCell()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $battlefield->getCellByCoordinate('A3')->setFlags(CellModel::FLAG_SHIP);

        /** @var Cell $cell */
        $cell = $this->invokeProcessPathMethod($battlefield, PathProcessor::PATH_DOWN, 'A1');
        $this->assertEquals($cell->getCoordinate(), 'A3');
    }

    private function invokeProcessPathMethod(Battlefield $battlefield, int $path, string $coordinate) : Cell
    {
        return $this->invokeMethod(static::$strategyProcessor, 'processPath', [$battlefield, $path, $coordinate]);
    }

    /**
     * @see     AIStrategyProcessor::processPaths
     * @test
     *
     * @depends processPathExpectedExceptionOnNotExistingCell
     * @depends processPathExpectedExceptionOnDeadNonShipCell
     * @depends processPathOnValidCell
     * @depends processPathOnValidDeadShipCell
     */
    public function processPathsComplete()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_DEAD_SHIP);

        $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
        /** as battlefield is mocked having all cells STATE_WATER_LIVE state */
        $this->assertCount(4, $cells);
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);

        /** as LEFT (A1) cell is dead */
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
        $this->assertCount(3, $cells);

        /** as entire horizontal row is dead (A1-J10) cell is dead */
        for ($letter = 'C'; $letter < 'G'; $letter++) {
            $battlefield->getCellByCoordinate("{$letter}2")->setFlags(CellModel::FLAG_DEAD_SHIP);
            $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
            $this->assertCount(3, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('C2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        ...
//        $battlefield->getCellByCoordinate('F2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('G2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
        $this->assertCount(2, $cells);

        /** as top (B1) cell is dead also */
        $battlefield->getCellByCoordinate('B1')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
        $this->assertCount(1, $cells);

        /** as vertical (B1-B10) and horizontal (A1-J10) rows contains only dead cells */
        for ($digit = 3; $digit < 7; $digit++) {
            $battlefield->getCellByCoordinate("B{$digit}")->setFlags(CellModel::FLAG_DEAD_SHIP);
            $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
            $this->assertCount(1, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('B3')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        ...
//        $battlefield->getCellByCoordinate('B6')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('B7')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->processPaths([$battlefield->getCellByCoordinate('B2'), PathProcessor::$primaryPaths]);
        $this->assertEmpty($cells);
    }

    /**
     * @see AIStrategyProcessor::processPaths
     *
     * @param array $args
     *
     * @return Cell[]
     */
    private function processPaths(array $args) : array
    {
        return $this->invokeMethod(static::$strategyProcessor, 'processPaths', $args);
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processPathsComplete
     */
    public function processHorizontalStrategy()
    {
        $this->verifyCellsByStrategy(['A2', 'C2'], AIStrategyProcessor::STRATEGY_HORIZONTAL);
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processPathsComplete
     */
    public function processVerticalStrategy()
    {
        $this->verifyCellsByStrategy(['B1', 'B3'], AIStrategyProcessor::STRATEGY_VERTICAL);
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processHorizontalStrategy
     * @depends processVerticalStrategy
     */
    public function processBothStrategy()
    {
        $this->verifyCellsByStrategy(['A2', 'C2', 'B1', 'B3'], AIStrategyProcessor::STRATEGY_BOTH);
    }

    private function verifyCellsByStrategy(array $expectedCoordinates, int $strategy)
    {
        $cells = static::$strategyProcessor->process(MockFactory::getBattlefieldMock()->getCellByCoordinate('B2'), $strategy);

        $this->assertCount(count($expectedCoordinates), $cells);
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);

        foreach ($cells as $cell) {
            $this->assertContains($cell->getCoordinate(), $expectedCoordinates);
        }
    }
}
