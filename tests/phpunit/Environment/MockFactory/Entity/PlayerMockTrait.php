<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory;

use EM\GameBundle\Entity\Player;
use EM\GameBundle\Entity\PlayerType;

/**
 * @since 7.0
 */
trait PlayerMockTrait
{
    use PlayerTypeMockTrait;

    private function getPlayerMock(string $name, PlayerType $type = null) : Player
    {
        return (new Player())
            ->setName($name)
            ->setType($type ?? $this->getPlayerTypeMock($type));
    }
}
