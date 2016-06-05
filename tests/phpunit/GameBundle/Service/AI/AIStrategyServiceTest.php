<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyService;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory;

/**
 * @see AIStrategyService
 */
class AIStrategyServiceTest extends IntegrationTestSuite
{
    /**
     * @var AIStrategyService
     */
    private $strategyService;

    protected function setUp()
    {
        $this->strategyService = static::$container->get('battleship_game.service.ai_strategy');
    }

    /**
     * @see AIStrategyService::chooseCells()
     * @test
     */
    public function chooseCellsOnNoDeadCellsInBattlefield()
    {
        $battlefield = MockFactory::getBattlefieldMock();

        $cells = $this->strategyService->chooseCells($battlefield);
        $this->assertCount(0, $cells);

        $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_DEAD_SHIP);
    }
}
