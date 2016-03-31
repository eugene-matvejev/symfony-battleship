<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyProcessor;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Service\CoordinateServiceMockTrait;

/**
 * @see AIStrategy
 */
class AIStrategyProcessorTest extends ExtendedTestSuite
{
    use BattlefieldMockTrait, CoordinateServiceMockTrait;
    /**
     * @var AIStrategyProcessor
     */
    private $strategyProcessor;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyProcessor = $this->getContainer()->get('battleship.game.services.ai.strategy.processor');
    }

    /**
     * @see AIStrategyProcessor::processCoordinates
     * @test
     */
    public function processCoordinates()
    {
        $cellStates = $this->getContainer()->get('battleship.game.services.cell.model')->getAllStates();

        $battlefield = $this->getBattlefieldMock();
        $battlefield->getCellByCoordinate('B2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $service = $this->getCoordinateServiceMock($battlefield->getCellByCoordinate('B2'));

        $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
        /** as battlefield is mocked having all cells STATE_WATER_LIVE state */
        $this->assertCount(4, $cells);
        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);

        /** as LEFT (A1) cell is dead */
        $battlefield->getCellByCoordinate('A2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
        $this->assertCount(3, $cells);

        /** as entire horizontal row is dead (A1-J10) cell is dead */
        for ($letter = 'C'; $letter < 'J'; $letter++) {
            $battlefield->getCellByCoordinate("{$letter}2")->setState($cellStates[CellModel::STATE_SHIP_DIED]);
            $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
            $this->assertCount(3, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('C2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('D2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('E2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('F2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('G2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('H2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('I2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('J2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
        $this->assertCount(2, $cells);

        /** as top (B1) cell is dead also */
        $battlefield->getCellByCoordinate('B1')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
        $this->assertCount(1, $cells);

        /** as vertical (B1-B10) and horizontal (A1-J10) rows contains only dead cells */
        for ($digit = 3; $digit < 10; $digit++) {
            $battlefield->getCellByCoordinate("B{$digit}")->setState($cellStates[CellModel::STATE_SHIP_DIED]);
            $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
            $this->assertCount(1, $cells);
        }
        /** left for explanation purposes */
//        $battlefield->getCellByCoordinate('B3')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B4')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B5')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B6')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B7')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B8')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
//        $battlefield->getCellByCoordinate('B9')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $battlefield->getCellByCoordinate('B10')->setState($cellStates[CellModel::STATE_SHIP_DIED]);
        $cells = $this->invokeStrategyMethod([$battlefield, $this->getBasicCoordinates($service)]);
        $this->assertEmpty($cells);
    }

    /**
     * @see     AIStrategyProcessor::process
     * @test
     *
     * @depends processCoordinates
     */
    public function processHorizontalStrategy()
    {
        $cells = $this->strategyProcessor->process(
            $this->getBattlefieldMock()->getCellByCoordinate('B2'),
            AIStrategyProcessor::STRATEGY_HORIZONTAL
        );

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
        $cells = $this->strategyProcessor->process(
            $this->getBattlefieldMock()->getCellByCoordinate('B2'),
            AIStrategyProcessor::STRATEGY_VERTICAL
        );

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
        $cells = $this->strategyProcessor->process(
            $this->getBattlefieldMock()->getCellByCoordinate('B2'),
            AIStrategyProcessor::STRATEGY_BOTH
        );

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(4, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
        $this->assertEquals('B1', $cells[2]->getCoordinate());
        $this->assertEquals('B3', $cells[3]->getCoordinate());
    }

    /**
     * @param CoordinateService $service
     *
     * @return CoordinateService[]
     */
    private function getBasicCoordinates(CoordinateService $service) : array
    {
        return [
            clone $service->setWay(CoordinateService::WAY_UP),
            clone $service->setWay(CoordinateService::WAY_DOWN),
            clone $service->setWay(CoordinateService::WAY_LEFT),
            clone $service->setWay(CoordinateService::WAY_RIGHT)
        ];
    }

    private function invokeStrategyMethod(array $args) : array
    {
        return $this->invokePrivateMethod(AIStrategyProcessor::class, $this->strategyProcessor, 'processCoordinates', $args);
    }
}