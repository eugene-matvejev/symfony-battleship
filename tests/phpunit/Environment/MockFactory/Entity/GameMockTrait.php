<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Entity\Game;

/**
 * @since 7.0
 */
trait GameMockTrait
{
    use BattlefieldMockTrait;

    private function getGameMock(int $battlefields = 2) : Game
    {
        $game = new Game();

        for($i = 0; $i < $battlefields; $i++) {
            $game->addBattlefield($this->getBattlefieldMock());
        }

        return $game;
    }
}
