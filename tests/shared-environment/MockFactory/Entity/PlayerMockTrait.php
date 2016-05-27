<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 7.0
 */
trait PlayerMockTrait
{
    protected function getPlayerMock(string $name, int $flags = PlayerModel::FLAG_NONE) : Player
    {
        return (new Player())
            ->setName($name)
            ->setFlags($flags);
    }

    protected function getAIPlayerMock(string $name) : Player
    {
        return $this->getPlayerMock($name, PlayerModel::FLAG_AI_CONTROLLED);
    }
}
