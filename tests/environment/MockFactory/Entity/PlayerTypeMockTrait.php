<?php

namespace EM\Tests\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 7.0
 */
trait PlayerTypeMockTrait
{
    protected function getPlayerTypeMock(int $type = PlayerModel::TYPE_HUMAN) : PlayerType
    {
        return (new PlayerType())
            ->setId($type);
    }
}
