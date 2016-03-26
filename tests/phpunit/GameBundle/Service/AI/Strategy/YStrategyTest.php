<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\AI\Strategy\YStrategy;

/**
 * @see YStrategy
 */
class YStrategyTest extends AbstractStrategyTest
{
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
        $cells = $this->strategyService->verify($this->getBattlefieldMock()->getCellByCoordinate('B2'));

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(2, $cells);
        $this->assertEquals('B1', $cells[0]->getCoordinate());
        $this->assertEquals('B3', $cells[1]->getCoordinate());
    }
}
