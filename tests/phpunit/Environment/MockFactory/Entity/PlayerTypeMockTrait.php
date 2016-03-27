<?php

namespace EM\Tests\PHPUnit\Environment\MockFactory\Entity;

use EM\GameBundle\Entity\PlayerType;
use EM\GameBundle\Model\PlayerModel;

/**
 * @since 7.0
 */
trait PlayerTypeMockTrait
{
    protected function getPlayerTypeMock(int $type = null) : PlayerType
    {
        return (new PlayerType())
            ->setId($type ?? PlayerModel::TYPE_HUMAN);
    }
}
