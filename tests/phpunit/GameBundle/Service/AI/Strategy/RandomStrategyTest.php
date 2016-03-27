<?php

namespace EM\Tests\PHPUnit\GameBundle\Service\AI\Strategy;

use EM\GameBundle\Entity\Cell;
use EM\GameBundle\Service\AI\Strategy\RandomStrategy;

/**
 * @see RandomStrategy
 */
class RandomStrategyTest extends AbstractStrategyTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->strategyService = $this->getContainer()->get('battleship.game.services.ai.rand.strategy.service');
    }

    /**
     * @see RandomStrategy::verify()
     * @test
     */
    public function verify()
    {
        $cells = $this->strategyService->verify($this->getBattlefieldMock()->getCellByCoordinate('B2'));

        $this->assertContainsOnlyInstancesOf(Cell::class, $cells);
        $this->assertCount(4, $cells);
        $this->assertEquals('A2', $cells[0]->getCoordinate());
        $this->assertEquals('C2', $cells[1]->getCoordinate());
        $this->assertEquals('B1', $cells[2]->getCoordinate());
        $this->assertEquals('B3', $cells[3]->getCoordinate());
    }
}
