<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Service\AI\Strategy\XStrategy;

class XStrategyTest extends AbstractStrategyTest
{
    /**
     * @var XStrategy
     */
    protected $strategyService;

    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.x.strategy.service');
    }

    /**
     * @see RandomStrategy::verify()
     * @test
     */
    public function verify()
    {
        $this->strategyService->getCellModel()->indexCells($this->getMockedBattlefield());
        $cells = $this->strategyService->verify($this->getMockedCell());

        $this->assertCount(2, $cells);
    }
}
