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
    use PlayerTypeMockTrait;

    protected function getPlayerMock(string $name, PlayerType $type = null) : Player
    {
        return (new Player())
            ->setName($name)
            ->setType($type ?? $this->getPlayerTypeMock());
    }

    protected function getCPUPlayerMock(string $name) : Player
    {
        return $this->getPlayerMock($name, $this->getPlayerTypeMock(PlayerModel::TYPE_CPU));
    }
}
