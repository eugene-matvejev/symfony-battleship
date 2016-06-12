<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

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
    protected static $aiStrategyService;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$aiStrategyService = static::$container->get('battleship_game.service.ai_strategy');
    }

    /**
     * @see AIStrategyService::chooseCells()
     * @test
     */
    public function chooseCellsOnNoDeadCellsInBattlefield()
    {
        $this->assertEmpty(static::$aiStrategyService->chooseCells(MockFactory::getBattlefieldMock()));
    }
}
