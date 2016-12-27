<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI;

use EM\GameBundle\Service\AI\AIStrategyProcessor;
use EM\GameBundle\Service\AI\AIStrategyService;
use EM\GameBundle\Service\CoordinateSystem\PathProcessor;
use EM\Tests\Environment\AbstractKernelTestSuite;
use EM\Tests\Environment\Factory\MockFactory;

/**
 * @see AIStrategyService
 */
class AIStrategyServiceTest extends AbstractKernelTestSuite
{
    /**
     * @var AIStrategyService
     */
    private static $aiStrategyService;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        static::$aiStrategyService = static::$container->get('em.game_bundle.service.ai_strategy');
    }

    /**
     * @see AIStrategyService::strategyMap
     * @test
     */
    public function strategyMap()
    {
        $this->assertCount(4, AIStrategyService::$strategyMap);
        foreach (AIStrategyService::$strategyMap as $path => $strategy) {
            $this->assertContains($path, PathProcessor::$primaryPaths);

            /** if path is HORIZONTAL, strategy should be HORIZONTAL otherwise VERTICAL @see AIStrategyService::$strategyMap contains only UP|DOWN|LEFT|RIGHT paths */
            $expectedStrategy = $path === PathProcessor::PATH_LEFT || $path === PathProcessor::PATH_RIGHT
                ? AIStrategyProcessor::STRATEGY_HORIZONTAL
                : AIStrategyProcessor::STRATEGY_VERTICAL;

            $this->assertEquals($expectedStrategy, $strategy);
            $this->assertEquals($expectedStrategy, $strategy);
        }
    }

    /**
     * @see AIStrategyService::chooseCells
     * @test
     */
    public function chooseCellsOnNoDeadCellsInBattlefield()
    {
        $this->assertEmpty(static::$aiStrategyService->chooseCells(MockFactory::getBattlefieldMock()));
    }
}
