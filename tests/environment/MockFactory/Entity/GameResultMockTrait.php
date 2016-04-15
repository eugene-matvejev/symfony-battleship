<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\GameResult;

/**
 * @since 7.0
 */
trait GameResultMockTrait
{
    protected function getGameResultMock() : GameResult
    {
        return new GameResult();
    }
}
