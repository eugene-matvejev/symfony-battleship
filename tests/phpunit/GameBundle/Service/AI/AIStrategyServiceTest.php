<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Service\AI\AIStrategyService;
use EM\Tests\Environment\IntegrationTestSuite;
use EM\Tests\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see AIStrategyService
 */
class AIStrategyServiceTest extends IntegrationTestSuite
{
    use BattlefieldMockTrait;
    /**
     * @var AIStrategyService
     */
    private $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = static::$container->get('battleship.game.services.ai.strategy.service');
    }

    /**
     * @see AIStrategyService::chooseCells()
     * @test
     */
    public function chooseCellsOnNoDeadCellsInBattlefield()
    {
        $battlefield = $this->getBattlefieldMock();

        $cells = $this->strategyService->chooseCells($battlefield);
        $this->assertCount(0, $cells);

        $battlefield->getCellByCoordinate('B2')->setFlags(CellModel::FLAG_DEAD_SHIP);
    }
}
