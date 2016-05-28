<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyProcessor;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see AIStrategy
 */
class AIStrategyProcessorTest extends IntegrationTestSuite
{
    /**
     * @var AIStrategyProcessor
     */
    private $strategyProcessor;

    protected function setUp()
    {
        $this->strategyProcessor = static::$container->get('battleship.game.services.ai.strategy.processor');
    }

    /**
     * @see AIStrategyProcessor::processCoordinates
     * @test
     */
    public function processCoordinates()
    {
        $battlefield = MockFactory::getBattlefieldMock();
        $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_DEAD_SHIP);

        $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
        /** as battlefield is mocked having all cells STATE_WATER_LIVE state */
        $this->assertCount(4, $cells);
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);

        /** as LEFT (A1) cell is dead */
        $battlefield->getCellByCoordinate('A2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
        $this->assertCount(3, $cells);

        /** as entire horizontal row is dead (A1-J10) cell is dead */
        for ($letter = 'C'; $letter < 'G'; $letter++) {
            $battlefield->getCellByCoordinate("{$letter}2")->setFlags(CellModel::FLAG_DEAD_SHIP);
            $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
            $this->assertCount(3, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('C2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        ...
//        $battlefield->getCellByCoordinate('F2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('G2')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
        $this->assertCount(2, $cells);

        /** as top (B1) cell is dead also */
        $battlefield->getCellByCoordinate('B1')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
        $this->assertCount(1, $cells);

        /** as vertical (B1-B10) and horizontal (A1-J10) rows contains only dead cells */
        for ($digit = 3; $digit < 7; $digit++) {
            $battlefield->getCellByCoordinate("B{$digit}")->setFlags(CellModel::FLAG_DEAD_SHIP);
            $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
            $this->assertCount(1, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('B3')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        ...
//        $battlefield->getCellByCoordinate('B6')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('B7')->setFlags(CellModel::FLAG_DEAD_SHIP);
        $cells = $this->invokeProcessCoordinatesMethod([$battlefield->getCellByCoordinate('B2'), PathProcessor::PRIMARY_PATHS]);
        $this->assertEmpty($cells);
    }

    private function invokeProcessCoordinatesMethod(array $args) : array
    {
        return $this->invokeMethod($this->strategyProcessor, 'processCoordinates', $args);
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processCoordinates
     */
    public function processHorizontalStrategy()
    {
        $cells = $this->invokeProcessMethod(AIStrategyProcessor::STRATEGY_HORIZONTAL);

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(2, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processCoordinates
     */
    public function processVerticalStrategy()
    {
        $cells = $this->invokeProcessMethod(AIStrategyProcessor::STRATEGY_VERTICAL);

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(2, $cells);
        $this->assertEquals('B1', $cells[0]->getCoordinate());
        $this->assertEquals('B3', $cells[1]->getCoordinate());
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processHorizontalStrategy
     * @depends processVerticalStrategy
     * @depends processCoordinates
     */
    public function processBothStrategy()
    {
        $cells = $this->invokeProcessMethod(AIStrategyProcessor::STRATEGY_BOTH);

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(4, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
        $this->assertEquals('B1', $cells[2]->getCoordinate());
        $this->assertEquals('B3', $cells[3]->getCoordinate());
    }

    /**
     * @param int $strategy
     *
     * @return Cell[]
     */
    private function invokeProcessMethod(int $strategy) : array
    {
        return $this->strategyProcessor->process(MockFactory::getBattlefieldMock()->getCellByCoordinate('B2'), $strategy);
    }
}
