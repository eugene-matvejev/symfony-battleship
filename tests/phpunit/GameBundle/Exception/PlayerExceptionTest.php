<?php

namespace EM\Tests\PHPUnit\GameBundle\Exception;

use EM\GameBundle\Exception\PlayerException;
use EM\GameBundle\Model\GameModel;

/**
 * @see PlayerException
 */
class PlayerExceptionTest extends CellExceptionTest
{
    /**
     * @see GameModel::playerTurn
     * @test
     *
     * @expectedException \EM\GameBundle\Exception\PlayerException
     */
    public function playerTurn()
    {
        $player = $this->getPlayerMock('', $this->getPlayerTypeMock(-1));
        $battlefield = $this->getBattlefieldMock();
        $battlefield->setPlayer($player);

        $this->invokePrivateMethod(
            GameModel::class,
            $this->gameModel,
            'playerTurn', [$battlefield, 'A0']
        );
    }
}
