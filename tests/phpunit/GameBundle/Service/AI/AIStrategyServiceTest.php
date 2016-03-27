<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyService;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see AIStrategyService
 */
class AIStrategyServiceTest extends ExtendedTestSuite
{
    use BattlefieldMockTrait;
    /**
     * @var AIStrategyService
     */
    private $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.strategy.service');
    }

    /**
     * @see AIStrategyService::attack()
     * @test
     */
    public function attack()
    {
        $cellStates = $this->getContainer()->get('battleship.game.services.cell.model')->getAllStates();
        $battlefield = $this->getBattlefieldMock();
        $cells = $this->strategyService->attack($battlefield);
        $this->assertCount(0, $cells);

        $battlefield->getCellByCoordinate('B2')->setState($cellStates[CellModel::STATE_SHIP_DIED]);

//        $cells = $this->strategyService->attack($battlefield);
//        $this->assertCount(4, $cells);
//
//        foreach ($battlefield->getCells() as $cell) {
//            if ($cell->getState()->getId() !== CellModel::STATE_SHIP_DIED || $this->isShipDead($cell)) {
//                continue;
//            }
//
//            switch ($this->decideStrategy($cell)) {
//                case self::STRATEGY_X:
//                    return $this->xStrategy->verify($cell);
//                case self::STRATEGY_Y:
//                    return $this->yStrategy->verify($cell);
//            }
//
//            return $this->randStrategy->verify($cell);
//        }
//
//        return [];
    }
}
