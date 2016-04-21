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
    protected function getPlayerMock(string $name, int $mask = PlayerModel::MASK_NONE) : Player
    {
        return (new Player())
            ->setName($name)
            ->setMask($mask);
    }

    protected function getCPUPlayerMock(string $name) : Player
    {
        return $this->getPlayerMock($name, PlayerModel::MASK_AI_CONTROLLED);
    }
}
