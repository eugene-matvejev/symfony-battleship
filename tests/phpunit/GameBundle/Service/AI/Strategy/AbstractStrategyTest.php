<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\Strategy\AbstractStrategy;
use EM\GameBundle\Service\AI\Strategy\RandomStrategy;
use EM\GameBundle\Service\CoordinateSystem\CoordinateService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;
use EM\Tests\PHPUnit\Environment\MockFactory\Service\CoordinateServiceMockTrait;

/**
 * @see AbstractStrategy
 */
class AbstractStrategyTest extends ExtendedTestSuite
{
    use BattlefieldMockTrait, CoordinateServiceMockTrait;
    /**
     * @var RandomStrategy
     */
    protected $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.rand.strategy.service');
    }

    /**
     * @see AbstractStrategy::verifyByCoordinates
     * @test
     */
    public function verifyByCoordinates()
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

    protected function invokeStrategyMethod(array $args) : array
    {
        return $this->invokePrivateMethod(RandomStrategy::class, $this->strategyService, 'verifyByCoordinates', $args);
    }

    protected function getBasicCoordinates(CoordinateService $service) : array
    {
        return [
            clone $service->setWay(CoordinateService::WAY_UP),
            clone $service->setWay(CoordinateService::WAY_DOWN),
            clone $service->setWay(CoordinateService::WAY_LEFT),
            clone $service->setWay(CoordinateService::WAY_RIGHT)
        ];
    }
}
