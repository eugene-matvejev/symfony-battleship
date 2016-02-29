<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Service\AI\Strategy\YStrategy;

class YStrategyTest extends AbstractStrategyTest
{
    /**
     * @var YStrategy
     */
    protected $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.y.strategy.service');
    }

    /**
     * @see YStrategy::verify()
     * @test
     */
    public function verify()
    {
        $this->strategyService->getCellModel()->indexCells($this->getMockedBattlefield());
        $cells = $this->strategyService->verify($this->getMockedCell());

        $this->assertCount(2, $cells);
    }
}
