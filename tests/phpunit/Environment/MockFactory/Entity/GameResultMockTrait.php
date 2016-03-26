<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Entity\GameResult;

/**
 * @since 7.0
 */
trait GameResultMockTrait
{
    private function getGameResultMock() : GameResult
    {
        return new GameResult();
    }
}
