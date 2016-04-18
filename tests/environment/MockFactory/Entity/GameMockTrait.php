<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Game;

/**
 * @since 7.0
 */
trait GameMockTrait
{
    use BattlefieldMockTrait;

    protected function getGameMock(int $players = 2, int $size = 7) : Game
    {
        $game = new Game();
        for ($i = 0; $i < $players; $i++) {
            $game->addBattlefield($this->getBattlefieldMock($size));
        }

        return $game;
    }
}
