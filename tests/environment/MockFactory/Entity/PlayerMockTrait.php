<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 7.0
 */
trait PlayerMockTrait
{
    protected function getPlayerMock(string $name, int $mask = PlayerModel::FLAG_NONE) : Player
    {
        return (new Player())
            ->setName($name)
            ->setFlags($mask);
    }

    protected function getCPUPlayerMock(string $name) : Player
    {
        return $this->getPlayerMock($name, PlayerModel::FLAG_AI_CONTROLLED);
    }
}
