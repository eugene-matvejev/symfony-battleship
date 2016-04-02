<?php

namespace EM\Tests\PHPUnit\GameBundle\Exception;

use EM\GameBundle\Exception\CellException;
use EM\GameBundle\Model\GameModel;
use EM\GameBundle\Model\PlayerModel;
use EM\Tests\PHPUnit\Environment\ExtendedTestSuite;
use EM\Tests\PHPUnit\Environment\MockFactory\Entity\BattlefieldMockTrait;

/**
 * @see CellException
 */
class CellExceptionTest extends ExtendedTestSuite
{
    use BattlefieldMockTrait;
    /**
     * @var GameModel
     */
    protected $gameModel;

    protected function setUp()
    {
        parent::setUp();
        $this->gameModel = $this->getContainer()->get('battleship.game.services.game.model');
    }

    /**
     * @see GameModel::playerTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\CellException
     */
    public function playerTurn()
    {
        $player = $this->getPlayerMock('', $this->getPlayerTypeMock(PlayerModel::TYPE_CPU));
        $battlefield = $this->getBattlefieldMock();
        $battlefield->setPlayer($player);

        $this->invokePrivateMethod(
            GameModel::class,
            $this->gameModel,
            'playerTurn', [$battlefield, 'A0']
        );
    }
}

