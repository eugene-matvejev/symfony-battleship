<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\GameResult;

/**
 * @since 7.0
 */
trait GameResultMockTrait
{
    use GameMockTrait;

    protected function getGameResultMock(int $players = 2, int $battlefieldSize = 7) : GameResult
    {
        $game = $this->getGameMock($players, $battlefieldSize);
        $gameResult = (new GameResult());
        $game->setResult($gameResult);

        return $gameResult;
    }
}
