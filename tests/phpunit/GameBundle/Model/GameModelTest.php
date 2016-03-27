<?php

namespace EM\Tests\PHPUnit\GameBundle\Model;

use EM\GameBundle\Model\CellModel;
use EM\GameBundle\Model\GameModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestCase;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\GameMockTrait;

/**
 * @see GameModel
 */
class GameModelTest extends ExtendedTestCase
{
    use GameMockTrait;
    /**
     * @var GameModel
     */
    private $gameModel;

    protected function setUp()
    {
        parent::setUp();
        $this->gameModel = $this->getContainer()->get('battleship.game.services.game.model');
    }

    /**
     * @see GameModel::detectVictory
     * @test
     */
    public function detectVictory()
    {
        $game = $this->getGameMock();
        $this->assertTrue($this->gameModel->detectVictory($game->getBattlefields()[0]));
        $this->assertNotNull($game->getResult());

        $game = $this->getGameMock();
        $battlefield = $game->getBattlefields()[0];
        $battlefield->getCellByCoordinate('A1')->setState($this->getCellStateMock(CellModel::STATE_SHIP_LIVE));
        $this->assertFalse($this->gameModel->detectVictory($game->getBattlefields()[0]));
        $this->assertNull($game->getResult());
    }
}
